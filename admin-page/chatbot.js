const tableBody = document.querySelector('.interactions-table tbody');
tableBody.onclick = function(e) {
  // Check if the click is on the export button or its child elements
  const exportButton = e.target.closest('.export-action');
  if (exportButton) {
    const row = exportButton.closest('tr');
    const rowData = {
      id: row.cells[0].textContent,
      company: row.cells[1].textContent,
      totalMessages: row.cells[2].textContent,
      usersAdded: row.cells[3].textContent,
      failedResponses: row.cells[4].textContent
    };

    const csvContent = `ID,Company,Total Messages,Users Added,Failed Responses\n${rowData.id},${rowData.company},${rowData.totalMessages},${rowData.usersAdded},${rowData.failedResponses}`;
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `chatbot-activity-${rowData.company}-${rowData.id}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    alert(`Exported data for ${rowData.company}`);
  } else if (e.target.closest('.flag-action')) {
    e.target.closest('.flag-action').classList.toggle('flagged');
  }
}; 