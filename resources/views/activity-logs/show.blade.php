<!-- Show Activity Log Modal -->
<div class="modal fade" id="showActivityLogModal" tabindex="-1" aria-labelledby="showActivityLogModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="showActivityLogModalLabel">Detail Activity Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label fw-bold">User</label>
                    <input type="text" id="logUser" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Activity</label>
                    <input type="text" id="logActivity" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea id="logDescription" class="form-control" rows="3" disabled></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Subject Type</label>
                    <input type="text" id="logSubjectType" class="form-control" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Data</label>
                    <pre id="logData" class="bg-light p-3 rounded border" style="max-height:300px; overflow:auto;"></pre>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">URL</label>
                        <input type="text" id="logUrl" class="form-control" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">IP Address</label>
                        <input type="text" id="logIp" class="form-control" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">User Agent</label>
                        <textarea id="logUserAgent" class="form-control" rows="2" disabled></textarea>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="text" id="logDate" class="form-control" disabled>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Time</label>
                        <input type="text" id="logTime" class="form-control" disabled>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {

            $(document).on('click', '.show-activity-log-btn', function() {
                let showUrl = $(this).data('show-route');

                $.ajax({
                    url: showUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {

                            let log = response.data;

                            $('#logUser').val(log.user ? log.user.name : '-');
                            $('#logActivity').val(log.activity ?? '-');
                            $('#logDescription').val(log.description ?? '-');
                            $('#logSubjectType').val(log.subject_type ?? '-');

                            // FORMAT JSON DATA
                            let parsedData = '-';
                            try {
                                parsedData = log.data ?
                                    JSON.stringify(JSON.parse(log.data), null, 4) :
                                    '-';
                            } catch (e) {
                                parsedData = log.data ?? '-';
                            }
                            $("#logData").text(parsedData);

                            $('#logUrl').val(log.url ?? '-');
                            $('#logIp').val(log.ip_address ?? '-');
                            $('#logUserAgent').val(log.user_agent ?? '-');
                            $('#logDate').val(log.date ?? '-');
                            $('#logTime').val(log.time ?? '-');

                            $('#showActivityLogModal').modal('show');
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Gagal mengambil data log", "error");
                    }
                });
            });

        });
    </script>
@endpush
