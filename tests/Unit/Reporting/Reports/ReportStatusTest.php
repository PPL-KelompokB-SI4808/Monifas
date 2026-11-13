<?php

use App\Enums\ReportStatus;

test('enum report status memiliki semua nilai status siklus hidup laporan', function () {
    expect(ReportStatus::Pending->value)->toBe('pending')
        ->and(ReportStatus::Verified->value)->toBe('verified')
        ->and(ReportStatus::Assigned->value)->toBe('assigned')
        ->and(ReportStatus::InProgress->value)->toBe('in_progress')
        ->and(ReportStatus::Completed->value)->toBe('completed')
        ->and(ReportStatus::Rejected->value)->toBe('rejected');
});

test('enum report status memiliki label bahasa indonesia yang tepat', function () {
    expect(ReportStatus::Pending->label())->toBe('Menunggu Verifikasi')
        ->and(ReportStatus::Verified->label())->toBe('Diverifikasi')
        ->and(ReportStatus::Assigned->label())->toBe('Ditugaskan')
        ->and(ReportStatus::InProgress->label())->toBe('Dalam Pengerjaan')
        ->and(ReportStatus::Completed->label())->toBe('Selesai')
        ->and(ReportStatus::Rejected->label())->toBe('Ditolak');
});

test('enum report status helper methods bekerja dengan benar', function () {
    expect(ReportStatus::Pending->canBeVerified())->toBeTrue()
        ->and(ReportStatus::Verified->canBeVerified())->toBeFalse()
        ->and(ReportStatus::Verified->canBeAssigned())->toBeTrue()
        ->and(ReportStatus::Pending->canBeAssigned())->toBeFalse()
        ->and(ReportStatus::Completed->isClosed())->toBeTrue()
        ->and(ReportStatus::Rejected->isClosed())->toBeTrue()
        ->and(ReportStatus::InProgress->isClosed())->toBeFalse();
});
