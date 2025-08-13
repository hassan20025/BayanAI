document.addEventListener("DOMContentLoaded", () => {
  function setupThemeToggle() {
    const themeToggle = document.getElementById("theme-toggle");
    const sunIcon = document.getElementById("sun-icon");
    const moonIcon = document.getElementById("moon-icon");
    const htmlElement = document.documentElement;
    if (!themeToggle) return;
    // Function to set the theme and update icons
    const setTheme = (theme) => {
      if (theme === "light") {
        htmlElement.classList.add("light");
        localStorage.setItem("theme", "light");
        if (sunIcon) sunIcon.style.display = "none";
        if (moonIcon) moonIcon.style.display = "block";
      } else {
        htmlElement.classList.remove("light");
        localStorage.setItem("theme", "dark");
        if (sunIcon) sunIcon.style.display = "block";
        if (moonIcon) moonIcon.style.display = "none";
      }
    };
    // Check for saved theme preference or default to dark
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme) {
      setTheme(savedTheme);
    } else {
      setTheme("dark");
    }
    // Remove previous listeners
    themeToggle.onclick = null;
    themeToggle.addEventListener("click", () => {
      const isLight = htmlElement.classList.contains("light");
      setTheme(isLight ? "dark" : "light");
    });
  }

  function setupSearchFilter() {
    const searchInput = document.querySelector('.search-input');
    const dashboardContent = document.querySelector('.dashboard-content');
    if (!searchInput || !dashboardContent) return;
    searchInput.addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();
      // Select all main content components that could be searched for
      const components = dashboardContent.querySelectorAll('.dashboard-card, .chart-card, .recent-interactions-card');
      let found = false;
      components.forEach(component => {
        const text = component.textContent.toLowerCase();
        if (query && text.includes(query)) {
          component.style.display = '';
          found = true;
        } else {
          component.style.display = 'none';
        }
      });
      // If search is empty, show all components
      if (!query) {
        components.forEach(component => component.style.display = '');
      }
    });
  }

  function setupSidebarNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href) {
          // Simple navigation to the page
          window.location.href = href;
        }
      });
    });
  }

  // Function to load data based on the current page
  function loadPageData() {
    const currentPath = window.location.pathname;
    console.log('Loading page data for current path:', currentPath);
    
    // Check if we're on the right page by looking for key elements
    if (currentPath.includes('admins.html')) {
      console.log('Loading admins data...');
      const adminsTable = document.getElementById('admins-table-body');
      if (adminsTable) {
        console.log('Admins table found, loading data...');
        loadAdminsData();
      } else {
        console.log('Admins table not found, retrying in 200ms...');
        setTimeout(() => loadAdminsData(), 200);
      }
    } else if (currentPath.includes('companies.html')) {
      console.log('Loading companies data...');
      const companiesTable = document.getElementById('companies-table-body');
      if (companiesTable) {
        console.log('Companies table found, loading data...');
        loadCompaniesData();
      } else {
        console.log('Companies table not found, retrying in 200ms...');
        setTimeout(() => loadCompaniesData(), 200);
      }
    } else if (currentPath.includes('chatbot-activity.html')) {
      console.log('Loading chats data...');
      const chatsTable = document.getElementById('chats-table-body');
      if (chatsTable) {
        console.log('Chats table found, loading data...');
        loadChatsData();
      } else {
        console.log('Chats table not found, retrying in 200ms...');
        setTimeout(() => loadChatsData(), 200);
      }
    } else if (currentPath.includes('index.html') || currentPath.endsWith('/admin-page/')) {
      console.log('Loading dashboard stats...');
      loadDashboardStats();
    }
  }

  // Function to load admins data
  async function loadAdminsData() {
    try {
      console.log('Loading admins data from script.js...');
      console.log('Current page title:', document.title);
      console.log('Looking for admins-table-body element...');
      
      const res = await fetch('/bayanAI/api/getAdmins.php');
      const admins = await res.json();
      
      console.log('Admins response:', admins);
      
      const adminsTableBody = document.getElementById('admins-table-body');
      console.log('Found admins table body:', adminsTableBody);
      
      if (!adminsTableBody) {
        console.error('Admins table body not found, retrying in 200ms...');
        // Retry after a short delay if table isn't ready
        setTimeout(() => loadAdminsData(), 200);
        return;
      }
      
      adminsTableBody.innerHTML = ''; // clear existing rows
      
      // Handle both array and object responses
      let adminsArray = admins;
      if (!Array.isArray(admins)) {
        if (admins.error) {
          console.error('Error from server:', admins.error);
          return;
        }
        adminsArray = [admins];
      }

      adminsArray.forEach(admin => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${admin.id}</td>
          <td>${admin.name}</td>
          <td>${admin.email}</td>
          <td>
            <button class="icon-button flag-action" title="Flag">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22V4a2 2 0 0 1 2-2h11.5a1.5 1.5 0 0 1 1.4 2.1L17 7l1.9 4.9A1.5 1.5 0 0 1 17.5 15H6a2 2 0 0 1-2-2z"/>
              </svg>
            </button>
                          <button class="icon-button delete-action" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                  <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
              </button>
          </td>
        `;
        adminsTableBody.appendChild(row);
      });
      
      // Setup delete functionality for new rows
      setupAdminsDeleteFunctionality();
      console.log('Admins loaded successfully:', adminsArray.length);
    } catch (err) {
      console.error('Failed to load admins:', err);
    }
  }

  // Function to load companies data
  async function loadCompaniesData() {
    try {
      const res = await fetch('/bayanAI/api/getCompanies.php');
      const companies = await res.json();
      
      const companiesTableBody = document.getElementById('companies-table-body');
      if (companiesTableBody) {
        companiesTableBody.innerHTML = ''; // clear existing rows
        
        // Handle both array and object responses
        let companiesArray = companies;
        if (!Array.isArray(companies)) {
          if (companies.error) {
            console.error('Error from server:', companies.error);
            return;
          }
          companiesArray = [companies];
        }

        companiesArray.forEach(company => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${company.id}</td>
            <td>${company.name}</td>
            <td>${company.email}</td>
            <td>${company.industry}</td>
            <td>${company.plan}</td>
            <td>${company.next_due}</td>
            <td>
              <button class="icon-button flag-action" title="Flag">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 22V4a2 2 0 0 1 2-2h11.5a1.5 1.5 0 0 1 1.4 2.1L17 7l1.9 4.9A1.5 1.5 0 0 1 17.5 15H6a2 2 0 0 1-2-2z"/>
                </svg>
              </button>
              <button class="icon-button delete-action" title="Delete">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
                  <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
              </button>
            </td>
          `;
          companiesTableBody.appendChild(row);
        });
        
        // Setup delete functionality for new rows
        setupCompaniesDeleteFunctionality();
        console.log('Companies loaded successfully:', companiesArray.length);
      }
    } catch (err) {
      console.error('Failed to load companies:', err);
    }
  }

  // Function to load chats data
  async function loadChatsData() {
    try {
      const res = await fetch('/bayanAI/api/getChats.php');
      const chats = await res.json();
      
      const chatsTableBody = document.getElementById('chats-table-body');
      if (chatsTableBody) {
        chatsTableBody.innerHTML = ''; // clear existing rows
        
        // Handle both array and object responses
        let chatsArray = chats;
        if (!Array.isArray(chats)) {
          if (chats.error) {
            console.error('Error from server:', chats.error);
            return;
          }
          chatsArray = [chats];
        }

        chatsArray.forEach(chat => {
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${chat.id}</td>
            <td>${chat.company_name}</td>
            <td>${chat.total_messeges ? chat.total_messeges.toLocaleString() : '0'}</td>
            <td>${chat.users_added || '0'}</td>
            <td>${chat.failed_responses || '0'}</td>
            <td>
              <div class="action-buttons">
                <button class="icon-button flag-action" title="Flag">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 22V4a2 2 0 0 1 2-2h11.5a1.5 1.5 0 0 1 1.4 2.1L17 7l1.9 4.9A1.5 1.5 0 0 1 17.5 15H6a2 2 0 0 1-2-2z"/>
                  </svg>
                </button>
                <button class="icon-button export-action" title="Export">
                  <img src="export.svg" alt="Export" style="width: 20px; height: 20px; filter: brightness(0) invert(1);">
                </button>
              </div>
            </td>
          `;
          chatsTableBody.appendChild(row);
        });
        
        // Setup export functionality for new rows
        setupChatsExportFunctionality();
        console.log('Chats loaded successfully:', chatsArray.length);
      }
    } catch (err) {
      console.error('Failed to load chats:', err);
    }
  }

  // Function to setup admins delete functionality
  function setupAdminsDeleteFunctionality() {
    const adminsTableBody = document.getElementById('admins-table-body');
    if (adminsTableBody) {
      // Remove existing event listeners
      const newAdminsTableBody = adminsTableBody.cloneNode(true);
      adminsTableBody.parentNode.replaceChild(newAdminsTableBody, adminsTableBody);
      
      newAdminsTableBody.addEventListener('click', async function (e) {
        if (e.target.closest('.delete-action')) {
          const row = e.target.closest('tr');
          const adminId = row.children[0].textContent;

          const confirmed = confirm("Are you sure you want to delete this admin?");
          if (!confirmed) return;

          try {
            const res = await fetch('/bayanAI/api/deleteAdmin.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: new URLSearchParams({ id: adminId })
            });
            const result = await res.json();

            if (result.success) {
              await loadAdminsData(); // Refresh the table
              console.log("Admin deleted from DB");
            } else {
              alert("Failed to delete admin: " + (result.error || 'Unknown error'));
            }
          } catch (err) {
            console.error("Error deleting admin:", err);
          }
        }
      });
    }
  }

  // Function to setup companies delete functionality
  function setupCompaniesDeleteFunctionality() {
    const companiesTableBody = document.getElementById('companies-table-body');
    if (companiesTableBody) {
      // Remove existing event listeners
      const newCompaniesTableBody = companiesTableBody.cloneNode(true);
      companiesTableBody.parentNode.replaceChild(newCompaniesTableBody, companiesTableBody);
      
      newCompaniesTableBody.addEventListener('click', async function (e) {
        if (e.target.closest('.delete-action')) {
          const row = e.target.closest('tr');
          const companyId = row.children[0].textContent;

          const confirmed = confirm("Are you sure you want to delete this company?");
          if (!confirmed) return;

          try {
            const res = await fetch('/bayanAI/api/deleteCompany.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: new URLSearchParams({ id: companyId })
            });
            const result = await res.json();

            if (result.success) {
              await loadCompaniesData(); // Refresh the table
              console.log("Company deleted from DB");
            } else {
              alert("Failed to delete company: " + (result.error || 'Unknown error'));
            }
          } catch (err) {
            console.error("Error deleting company:", err);
          }
        }
      });
    }
  }

  // Function to setup chats export functionality
  function setupChatsExportFunctionality() {
    const chatsTableBody = document.getElementById('chats-table-body');
    if (chatsTableBody) {
      // Remove existing event listeners
      const newChatsTableBody = chatsTableBody.cloneNode(true);
      chatsTableBody.parentNode.replaceChild(newChatsTableBody, chatsTableBody);
      
      newChatsTableBody.addEventListener('click', async function (e) {
        if (e.target.closest('.export-action')) {
          const row = e.target.closest('tr');
          const chatId = row.children[0].textContent;
          const companyName = row.children[1].textContent;
          const totalMessages = row.children[2].textContent;
          const usersAdded = row.children[3].textContent;
          const failedResponses = row.children[4].textContent;

          // Create detailed report format
          const currentDate = new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
          
          // Calculate success rate
          const totalMessagesNum = parseInt(totalMessages.replace(/,/g, ''));
          const failedResponsesNum = parseInt(failedResponses);
          const successRate = totalMessagesNum > 0 ? Math.round(((totalMessagesNum - failedResponsesNum) / totalMessagesNum) * 100) : 0;
          
          const csvContent = `Chatbot Activity Report
Generated: ${currentDate}
Company: ${companyName}
Chat ID: ${chatId}

ID,Company Name,Total Messages,Users Added,Failed Responses,Success Rate (%)
${chatId},"${companyName}",${totalMessages},${usersAdded},${failedResponses},${successRate}

Notes:
- Total Messages: ${totalMessages}
- Users Added: ${usersAdded}
- Failed Responses: ${failedResponses}
- Success Rate: ${successRate}%
- Export Date: ${currentDate}`;
          
          // Create and download CSV file
          const blob = new Blob([csvContent], { type: 'text/csv' });
          const url = window.URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = `chatbot-activity-${companyName}-${new Date().toISOString().split('T')[0]}.csv`;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
          
          console.log("Chat data exported successfully");
        }
      });
    }
  }

  function setupVolumeChartTooltip() {
    const chart = document.querySelector('.chart-volume svg');
    if (!chart) return;
    let tooltip = chart.querySelector('#volume-tooltip');
    let tooltipLabel;
    // If tooltip <g> is missing, create it
    if (!tooltip) {
      tooltip = document.createElementNS('http://www.w3.org/2000/svg', 'g');
      tooltip.setAttribute('id', 'volume-tooltip');
      tooltip.setAttribute('style', 'visibility:hidden;');
      const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
      rect.setAttribute('x', '0');
      rect.setAttribute('y', '0');
      rect.setAttribute('width', '80');
      rect.setAttribute('height', '30');
      rect.setAttribute('rx', '5');
      rect.setAttribute('ry', '5');
      rect.setAttribute('fill', '#1a1a2e');
      tooltip.appendChild(rect);
      tooltipLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      tooltipLabel.setAttribute('id', 'volume-tooltip-label');
      tooltipLabel.setAttribute('x', '40');
      tooltipLabel.setAttribute('y', '18');
      tooltipLabel.setAttribute('fill', 'white');
      tooltipLabel.setAttribute('font-size', '14');
      tooltipLabel.setAttribute('font-weight', 'bold');
      tooltipLabel.setAttribute('text-anchor', 'middle');
      tooltipLabel.textContent = 'Value';
      tooltip.appendChild(tooltipLabel);
      chart.appendChild(tooltip);
    } else {
      tooltipLabel = chart.querySelector('#volume-tooltip-label');
    }
    if (!tooltip || !tooltipLabel) {
      console.log('Tooltip or label not found in SVG');
      return;
    }
    // Only select data point dots (skip legend/axis dots)
    const dots = Array.from(chart.querySelectorAll('circle')).filter(dot => dot.getAttribute('fill') === '#60a5fa');
    // Example values for each dot (replace with real data if available)
    const values = ["13,721", "15,000", "12,500", "14,200", "16,000", "13,800", "15,600", "17,100", "14,900", "16,500"];
    // Hide tooltip initially
    tooltip.setAttribute('visibility', 'hidden');
    dots.forEach((dot, i) => {
      dot.style.cursor = 'pointer';
      dot.onmouseenter = null;
      dot.onmouseleave = null;
      dot.addEventListener('mouseenter', function(e) {
        const cx = parseFloat(dot.getAttribute('cx'));
        const cy = parseFloat(dot.getAttribute('cy'));
        tooltip.setAttribute('visibility', 'visible');
        tooltip.setAttribute('transform', `translate(${cx - 40},${cy - 45})`);
        tooltipLabel.textContent = values[i] || 'Value';
        chart.appendChild(tooltip);
      });
      dot.addEventListener('mouseleave', function(e) {
        tooltip.setAttribute('visibility', 'hidden');
      });
    });
    // Hide tooltip when mouse leaves the chart area
    chart.addEventListener('mouseleave', function(e) {
      tooltip.setAttribute('visibility', 'hidden');
    });
  }

  // User dropdown logic
  const userDropdown = document.querySelector('.user-dropdown');
  const userButton = userDropdown?.querySelector('.user-button');
  if (userButton && userDropdown) {
    userButton.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
    });
    document.addEventListener('click', function() {
      userDropdown.classList.remove('open');
    });
  }
  const logoutBtn = document.getElementById('logout-btn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      alert('Logging out...');
      // window.location.href = '/auth/login/index.html'; // Uncomment for real logout
    });
  }

  setupSidebarNavigation();
  setupThemeToggle();
  setupSearchFilter();
  setupVolumeChartTooltip();
  
  // Load initial data based on current page
  loadPageData();
});

// Simple page navigation - no SPA complexity needed

// --- Interactions Table: Flag, Delete, Toaster, and Filtering ---
const flaggedRows = new Set();

function showToaster(message) {
  const toaster = document.getElementById('toaster');
  if (!toaster) return;
  toaster.textContent = message;
  toaster.classList.add('show');
  setTimeout(() => {
    toaster.classList.remove('show');
  }, 2000);
}

function updateFlaggedButtonCount() {
  const flaggedBtn = document.querySelector('.interaction-button:nth-child(3)');
  if (flaggedBtn) {
    const count = flaggedRows.size;
    flaggedBtn.textContent = count > 0 ? `Flagged Interactions (${count})` : 'Flagged Interactions';
  }
}

function handleFlagClick(e) {
  const row = e.target.closest('tr[data-row-id]');
  if (!row) return;
  const rowId = row.getAttribute('data-row-id');
  if (flaggedRows.has(rowId)) {
    flaggedRows.delete(rowId);
    row.classList.remove('flagged');
    showToaster('Interaction unflagged.');
  } else {
    flaggedRows.add(rowId);
    row.classList.add('flagged');
    showToaster('Interaction flagged!');
  }
  updateFlaggedButtonCount();
}

function handleDeleteClick(e) {
  const row = e.target.closest('tr[data-row-id]');
  if (!row) return;
  row.remove();
  showToaster('Interaction deleted.');
  // Remove from flagged if it was flagged
  const rowId = row.getAttribute('data-row-id');
  flaggedRows.delete(rowId);
  updateFlaggedButtonCount();
}

function setupInteractionActions() {
  document.querySelectorAll('.flag-action').forEach(btn => {
    btn.onclick = handleFlagClick;
  });
  document.querySelectorAll('.delete-action').forEach(btn => {
    btn.onclick = handleDeleteClick;
  });
}

function showFlaggedInteractionsOnly() {
  document.querySelectorAll('.interactions-table tbody tr').forEach(row => {
    const rowId = row.getAttribute('data-row-id');
    if (flaggedRows.has(rowId)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function showAllInteractions() {
  document.querySelectorAll('.interactions-table tbody tr').forEach(row => {
    row.style.display = '';
  });
}

function setupFlaggedFilterButtons() {
  const buttons = document.querySelectorAll('.interaction-button');
  if (buttons.length < 3) return;
  // Recent Interactions
  buttons[0].onclick = () => {
    showAllInteractions();
    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[0].classList.add('active');
  };
  // All Interactions
  buttons[1].onclick = () => {
    showAllInteractions();
    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[1].classList.add('active');
  };
  // Flagged Interactions
  buttons[2].onclick = () => {
    showFlaggedInteractionsOnly();
    buttons.forEach(btn => btn.classList.remove('active'));
    buttons[2].classList.add('active');
  };
}

document.addEventListener('DOMContentLoaded', () => {
  setupInteractionActions();
  setupFlaggedFilterButtons();
});

// Setup interaction actions when page loads
document.addEventListener('DOMContentLoaded', () => {
  if (typeof setupInteractionActions === 'function') setupInteractionActions();
  if (typeof setupFlaggedFilterButtons === 'function') setupFlaggedFilterButtons();
});

// Optional: style flagged rows
const style = document.createElement('style');
style.textContent = `.interactions-table tr.flagged { background: rgba(245, 158, 66, 0.12); }`;
document.head.appendChild(style);

// Load Dashboard Statistics
async function loadDashboardStats() {
  try {
    const response = await fetch('/bayanAI/api/getDashboardStats.php');
    const data = await response.json();
    
    if (data.success) {
      // Update Total Users card
      const userCard = document.querySelector('.dashboard-card:nth-child(1)');
      if (userCard) {
        const userValue = userCard.querySelector('.card-value');
        const userChange = userCard.querySelector('.card-change');
        
        if (userValue) {
          userValue.textContent = data.users.total.toLocaleString();
        }
        
        if (userChange) {
          // Update percentage
          const percentSpan = userChange.querySelector('span');
          if (percentSpan) {
            percentSpan.textContent = Math.abs(data.users.change_percent) + '%';
          }
          
          // Update direction (increase/decrease)
          userChange.className = `card-change ${data.users.change_direction}`;
          
          // Update arrow icon
          const arrowIcon = userChange.querySelector('svg');
          if (arrowIcon) {
            if (data.users.change_direction === 'increase') {
              arrowIcon.innerHTML = '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>';
            } else {
              arrowIcon.innerHTML = '<path d="M7 7h10v10"/><path d="17 7 7 17"/>';
            }
          }
        }
      }
      
      // Update Total Chats card
      const chatCard = document.querySelector('.dashboard-card:nth-child(2)');
      if (chatCard) {
        const chatValue = chatCard.querySelector('.card-value');
        const chatChange = chatCard.querySelector('.card-change');
        
        if (chatValue) {
          chatValue.textContent = data.chats.total.toLocaleString();
        }
        
        if (chatChange) {
          // Update percentage
          const percentSpan = chatChange.querySelector('span');
          if (percentSpan) {
            percentSpan.textContent = Math.abs(data.chats.change_percent) + '%';
          }
          
          // Update direction (increase/decrease)
          chatChange.className = `card-change ${data.chats.change_direction}`;
          
          // Update arrow icon
          const arrowIcon = chatChange.querySelector('svg');
          if (arrowIcon) {
            if (data.chats.change_direction === 'increase') {
              arrowIcon.innerHTML = '<path d="M7 7h10v10"/><path d="M7 17 17 7"/>';
            } else {
              arrowIcon.innerHTML = '<path d="M7 7h10v10"/><path d="17 7 7 17"/>';
            }
          }
        }
      }
      
      console.log('Dashboard statistics loaded successfully');
    }
    
  } catch (error) {
    console.error('Error loading dashboard statistics:', error);
  }
}

// These functions are now handled by the main script functionality
// and are no longer needed as separate window functions
