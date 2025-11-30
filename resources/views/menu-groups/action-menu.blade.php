<!-- Action Modal -->
<div class="modal fade" id="actionMenuGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title">Choose Action</h5>
                <button type="button" class="btn-close position-absolute end-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex" id="action-buttons-container" style="gap: 0.5rem;">
                <button type="button" class="btn btn-warning edit-menu-group-btn" id="modal-edit-btn">
                    <i class="bx bx-edit"></i> Edit
                </button>

                <a href="#" class="btn btn-primary" id="modal-back-btn">
                    <i class="bx bx-list-ul"></i> Menu Items
                </a>

                <button type="button" class="btn btn-danger delete-menu-group-btn" id="modal-delete-btn">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        let selectedEditUrl = '';
        let selectedUpdateUrl = '';
        let selectedDeleteUrl = '';

        $(document).on('click', '.action-menu-group-btn', function() {
            selectedEditUrl = $(this).data('edit-route');
            selectedUpdateUrl = $(this).data('update-route');
            selectedDeleteUrl = $(this).data('delete-route');
            const menuItemUrl = $(this).data('menu-item-url');
            const routeData = $(this).data('route');

            // Tampilkan atau sembunyikan tombol "Menu Items" berdasarkan route
            if (!routeData || routeData === '-') {
                $('#modal-back-btn').attr('href', menuItemUrl).show();
            } else {
                $('#modal-back-btn').hide();
            }

            // Simpan semua tombol
            const buttons = [{
                    el: $('#modal-edit-btn'),
                    show: true
                },
                {
                    el: $('#modal-back-btn'),
                    show: !routeData || routeData === '-'
                },
                {
                    el: $('#modal-delete-btn'),
                    show: true
                }
            ];

            // Tampilkan / sembunyikan tombol
            buttons.forEach(btn => btn.el.toggle(btn.show));

            // Hitung tombol yang terlihat
            const visibleButtons = buttons.filter(btn => btn.show).length;
            console.log(visibleButtons);
            // Atur justify-content sesuai jumlah tombol
            if (visibleButtons === 2) {
                $('#action-buttons-container').css({
                    'justify-content': 'center',
                    'gap': '4.5rem'
                });
            } else {
                $('#action-buttons-container').css({
                    'justify-content': 'space-around',
                    'gap': '0.5rem'
                });
            }


            $('#actionMenuGroupModal').modal('show');
        });
    </script>
@endpush
