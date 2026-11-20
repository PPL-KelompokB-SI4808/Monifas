export default function supportingFeedback(initialRating = 5) {
    return {
        rating: initialRating,
        hoverRating: 0,

        setRating(val) {
            this.rating = val;
        },

        setHover(val) {
            this.hoverRating = val;
        },

        clearHover() {
            this.hoverRating = 0;
        },

        isActive(index) {
            const current = this.hoverRating || this.rating;
            return index <= current;
        },

        get ratingLabel() {
            const val = this.hoverRating || this.rating;
            switch (val) {
                case 1:
                    return '1 - Sangat Buruk (Sangat Tidak Puas)';
                case 2:
                    return '2 - Buruk (Kurang Puas)';
                case 3:
                    return '3 - Cukup (Biasa Saja)';
                case 4:
                    return '4 - Baik (Puas)';
                case 5:
                    return '5 - Sangat Baik (Sangat Puas)';
                default:
                    return 'Pilih nilai rating bintang';
            }
        }
    };
}
