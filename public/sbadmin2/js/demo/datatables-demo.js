// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').DataTable({
    responsive: false,   // agar kolom menyesuaikan layar
    autoWidth: false,   // hindari lebar otomatis yang bikin layout kacau
    scrollX: true      // jangan pakai scroll horizontal
  });
});