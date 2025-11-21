(function ($) {
    function toggleModal(show) {
        const modal = $('#hw-manual-modal');
        if (show) {
            modal.show();
        } else {
            modal.hide();
        }
    }

    function toggleMarketplace() {
        const channel = $('#hw_purchase_channel').val();
        if (channel === 'Marketplace') {
            $('#hw-marketplace-wrapper').show();
            $('#hw_marketplace_source').attr('required', 'required');
        } else {
            $('#hw-marketplace-wrapper').hide();
            $('#hw_marketplace_source').removeAttr('required').val('');
        }
    }

    function toggleDatePicker() {
        const mode = $('input[name="date_mode"]:checked').val();
        if (mode === 'choose') {
            $('#hw_purchase_date').show().attr('required', 'required');
        } else {
            $('#hw_purchase_date').hide().removeAttr('required');
        }
    }

    $(document).ready(function () {
        $('#hw-add-manual').on('click', function () {
            toggleModal(true);
        });

        $('.hw-close').on('click', function () {
            toggleModal(false);
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                toggleModal(false);
            }
        });

        $('#hw_purchase_channel').on('change', toggleMarketplace);
        $('input[name="date_mode"]').on('change', toggleDatePicker);

        toggleMarketplace();
        toggleDatePicker();
    });
})(jQuery);
