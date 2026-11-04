
export default function materialRepeater() {
    return {
        materials: [
            {
                id: Date.now(),
                material_name: '',
                material_type: '',
                unit: '',
                quantity: 1,
                unit_price: 0,
                notes: '',
            },
        ],

        addMaterial() {
            this.materials.push({
                id: Date.now() + Math.random(),
                material_name: '',
                material_type: '',
                unit: '',
                quantity: 1,
                unit_price: 0,
                notes: '',
            });
        },

        removeMaterial(index) {
            if (this.materials.length > 1) {
                this.materials.splice(index, 1);
            }
        },

        get totalMaterial() {
            return this.materials.reduce((total, material) => {
                const quantity = Number(material.quantity) || 0;
                const unitPrice = Number(material.unit_price) || 0;

                return total + (quantity * unitPrice);
            }, 0);
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                maximumFractionDigits: 2,
            }).format(Number(value) || 0);
        },
    };
}
