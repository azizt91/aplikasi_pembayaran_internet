// Call the dataTables jQuery plugin
// $(document).ready(function() {
//   $('#dataTable').DataTable();
// });

$(document).ready(function () {
    $('#dataTable').DataTable({
        "pageLength": 10, // Menampilkan 10 data per halaman, Anda bisa mengubah sesuai kebutuhan
        "lengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]], // Menambahkan opsi untuk menampilkan lebih banyak data
        "initComplete": function () {
            $('#dataTable').css('visibility', 'visible').hide().fadeIn();
        }
    });
});
