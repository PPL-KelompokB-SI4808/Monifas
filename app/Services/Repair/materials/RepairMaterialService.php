<?php

namespace App\Services\Repair\materials;

use App\Models\Repair;
use App\Models\RepairMaterial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RepairMaterialService
{
    /**
     * Mengambil seluruh material berdasarkan repair.
     */
    public function getByRepair(Repair $repair): Collection
    {
        return $repair->materials()->latest()->get();
    }

    /**
     * Menyimpan beberapa material sekaligus.
     */
    public function store(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $repair = Repair::findOrFail($data['repair_id']);

            $materials = [];

            foreach ($data['materials'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                $materials[] = [
                    'material_name' => $item['material_name'],
                    'material_type' => $item['material_type'],
                    'unit' => $item['unit'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $quantity * $unitPrice,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            return $repair->materials()->createMany($materials);
        });
    }

    /**
     * Memperbarui data material.
     */
    public function update(
        RepairMaterial $material,
        array $data
    ): RepairMaterial {
        return DB::transaction(function () use ($material, $data) {
            $quantity = (float) $data['quantity'];
            $unitPrice = (float) ($data['unit_price'] ?? 0);

            $material->update([
                'material_name' => $data['material_name'],
                'material_type' => $data['material_type'],
                'unit' => $data['unit'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $quantity * $unitPrice,
                'notes' => $data['notes'] ?? null,
            ]);

            return $material->refresh();
        });
    }

    /**
     * Menghapus material.
     */
    public function delete(RepairMaterial $material): bool
    {
        return DB::transaction(function () use ($material) {
            return (bool) $material->delete();
        });
    }

    /**
     * Menghitung total seluruh material pada satu repair.
     */
    public function calculateMaterialTotal(Repair $repair): float
    {
        return (float) $repair->materials()->sum('subtotal');
    }
}
