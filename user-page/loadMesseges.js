document.addEventListener("DOMContentLoaded", () => {
    fetch("../api/getMesseges.php")
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById("messages-table-body");
        tbody.innerHTML = "";
  
        data.forEach((row, index) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${new Date(row.time).toLocaleTimeString()}</td>
            <td>${row.username}</td>
            <td>${row.message}</td>
            <td>${row.response || "—"}</td>
            <td>
              <div class="action-buttons">
                <button class="icon-button flag-action" title="Flag">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                    <line x1="4" y1="22" x2="4" y2="15"/>
                  </svg>
                </button>
                                                  <button class="icon-button export-action" title="Export">
                   <img src="../admin-page/export.svg" alt="Export" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">
                 </button>
              </div>
            `;
          tbody.appendChild(tr);
        });
        
        // Setup action functionality after loading data
        setupMessagesActions();
      })
      .catch(err => {
        console.error("Error loading messages:", err);
      });
  });

// Setup action functionality for messages
function setupMessagesActions() {
  const tableBody = document.querySelector('.interactions-table tbody');
  
  tableBody.onclick = function(e) {
    // Check if the click is on the export button or its child elements
    const exportButton = e.target.closest('.export-action');
    if (exportButton) {
      const row = exportButton.closest('tr');
      const rowData = {
        time: row.cells[0].textContent,
        username: row.cells[1].textContent,
        message: row.cells[2].textContent,
        response: row.cells[3].textContent
      };

      const csvContent = `Time,User,Message,Response\n${rowData.time},${rowData.username},"${rowData.message}","${rowData.response}"`;
      const blob = new Blob([csvContent], { type: 'text/csv' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `message-${rowData.username}-${Date.now()}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);

      alert(`Exported message data for ${rowData.username}`);
    } else if (e.target.closest('.flag-action')) {
      const flagButton = e.target.closest('.flag-action');
      flagButton.classList.toggle('flagged');
      
      if (flagButton.classList.contains('flagged')) {
        alert('Message flagged!');
      } else {
        alert('Message unflagged!');
      }
    }
  };
}
  