<?php

use App\DTOs\UsersAndAccess\UserAddressData;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\UsersAndAccess\UserAddresses\UserAddressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new UserAddressService;
    $this->user = User::factory()->create();
});

test('service dapat membuat alamat baru untuk user', function () {
    $data = new UserAddressData(
        userId: $this->user->id,
        label: 'Kantor Pusat',
        buildingOrUnit: 'Gedung A Lt. 3',
        addressDetail: 'Jl. Jenderal Sudirman No. 12',
        contactPhone: '08123456789',
        postalCode: '10220',
        isPrimary: false,
    );

    $address = $this->service->createAddress($data);

    expect($address)->toBeInstanceOf(UserAddress::class);
    expect($address->label)->toBe('Kantor Pusat');
    expect($address->building_or_unit)->toBe('Gedung A Lt. 3');
    expect($address->is_primary)->toBeFalse();
    expect($address->user_id)->toBe($this->user->id);
});

test('service menonaktifkan is_primary alamat lain saat membuat alamat baru dengan is_primary true', function () {
    // Alamat 1 (primary awal)
    $address1 = UserAddress::factory()->create([
        'user_id' => $this->user->id,
        'label' => 'Alamat Lama',
        'is_primary' => true,
    ]);

    // Alamat 2 dibuat dengan is_primary = true
    $data2 = new UserAddressData(
        userId: $this->user->id,
        label: 'Alamat Baru Primer',
        buildingOrUnit: null,
        addressDetail: 'Jl. Merdeka No. 1',
        contactPhone: null,
        postalCode: null,
        isPrimary: true,
    );

    $address2 = $this->service->createAddress($data2);

    expect($address2->is_primary)->toBeTrue();
    expect($address1->fresh()->is_primary)->toBeFalse();
});

test('service menonaktifkan is_primary alamat lain milik user yang sama saat alamat diupdate menjadi is_primary true', function () {
    $address1 = UserAddress::factory()->create([
        'user_id' => $this->user->id,
        'label' => 'Alamat 1',
        'is_primary' => true,
    ]);

    $address2 = UserAddress::factory()->create([
        'user_id' => $this->user->id,
        'label' => 'Alamat 2',
        'is_primary' => false,
    ]);

    $updateData = new UserAddressData(
        userId: $this->user->id,
        label: 'Alamat 2 Diperbarui Primer',
        buildingOrUnit: 'Gedung B',
        addressDetail: 'Jl. Thamrin No. 5',
        contactPhone: '08987654321',
        postalCode: '10350',
        isPrimary: true,
    );

    $updated = $this->service->updateAddress($address2, $updateData);

    expect($updated->is_primary)->toBeTrue();
    expect($address1->fresh()->is_primary)->toBeFalse();
});

test('perubahan is_primary tidak mempengaruhi alamat milik user lain', function () {
    $otherUser = User::factory()->create();

    $otherUserPrimaryAddress = UserAddress::factory()->create([
        'user_id' => $otherUser->id,
        'label' => 'Alamat User Lain',
        'is_primary' => true,
    ]);

    $data = new UserAddressData(
        userId: $this->user->id,
        label: 'Alamat Utama User Saya',
        buildingOrUnit: null,
        addressDetail: 'Jl. Gatot Subroto No. 10',
        contactPhone: null,
        postalCode: null,
        isPrimary: true,
    );

    $myNewAddress = $this->service->createAddress($data);

    expect($myNewAddress->is_primary)->toBeTrue();
    expect($otherUserPrimaryAddress->fresh()->is_primary)->toBeTrue();
});

test('service dapat menghapus alamat', function () {
    $address = UserAddress::factory()->create([
        'user_id' => $this->user->id,
    ]);

    $deleted = $this->service->deleteAddress($address);

    expect($deleted)->toBeTrue();
    expect(UserAddress::find($address->id))->toBeNull();
});

test('service dapat mengeset alamat menjadi primary via setAsPrimary', function () {
    $address1 = UserAddress::factory()->create([
        'user_id' => $this->user->id,
        'is_primary' => true,
    ]);

    $address2 = UserAddress::factory()->create([
        'user_id' => $this->user->id,
        'is_primary' => false,
    ]);

    $this->service->setAsPrimary($address2);

    expect($address1->fresh()->is_primary)->toBeFalse();
    expect($address2->fresh()->is_primary)->toBeTrue();
});
