

document.addEventListener("DOMContentLoaded", () => {

  function handleRedirect() {
    fetch("http://localhost/BayanAI/api/users/me.php", {
      credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
        if (data.data.role !== "manager") {
          window.location.href = "/bayanai/homepage";
        }
        else {
          const username = document.querySelector(".user-name");
          username.innerHTML = data.data.username;
        }
    })
    .catch(error => {
      console.error("Error:", error);
      });
  } 
  handleRedirect();
  
  function checkIfSubscribed() {
    fetch("http://localhost/BayanAI/payment/PaymentStatus.php", {
      credentials: "include"
    })
    .then(res => res.json())
    .then(data => {
      console.log("subscription status: ", data.status);
        if (data.status === "unsubscribed") {
          window.location.href = "/BayanAI/payment/payment.php";
        }
    })
    .catch(error => {
      console.log("Error:", error);
    });
  } 

    checkIfSubscribed();

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
      if (!searchInput) return;
    
      searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
    
        // Decide what to filter depending on the page
        let items = [];
    
        // Case 1: index.html -> cards and charts
        if (document.querySelector('.dashboard-content')) {
          items = document.querySelectorAll('.dashboard-card, .chart-card, .recent-interactions-card');
        }
    
        // Case 2: users.html -> table rows
        if (document.querySelector('#users-table-body')) {
          items = document.querySelectorAll('#users-table-body tr');
        }
    
        // Case 3: messages.html -> table rows
        if (document.querySelector('#messages-table-body')) {
          items = document.querySelectorAll('#messages-table-body tr');
        }
    
        // Case 4: knowledge.html -> knowledge entries
        if (document.querySelector('#kb-table-body')) {
          items = document.querySelectorAll('#kb-table-body tr');
        }
    
        // Apply filter
        items.forEach(item => {
          const text = item.textContent.toLowerCase();
          if (query && !text.includes(query)) {
            item.style.display = 'none';
          } else {
            item.style.display = '';
          }
        });
      });
    }
    
    
  
    function setupSidebarNavigation() {
      const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
      const mainContainer = document.querySelector('.dashboard-main');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          const href = this.getAttribute('href');
          if (href && href !== 'index.html') {
            e.preventDefault();
           // For Chat page, navigate directly
if (href === 'chat/index.html' || href.includes('chat/')) {
  window.location.href = '/BayanAI/chat/index.html';
  return;
}

            // For knowledge.html, navigate directly to the page
            if (href === 'knowledge.html') {
              window.location.href = '/BayanAI/user-page/knowledge.html';
              return;
            }
            
            fetch(href)
              .then(res => res.text())
              .then(html => {
                // Extract the main content from the loaded page
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newMain = temp.querySelector('.dashboard-main');
                if (newMain && mainContainer) {
                  mainContainer.innerHTML = newMain.innerHTML;
                  // Update active class
                  document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
                  this.classList.add('active');
                                  // Update title
                document.title = temp.querySelector('title')?.innerText || document.title;
                
                // Load data based on the current page
                loadPageData(href);
                
                // Re-attach handlers for new sidebar links, theme toggle, and search
                setupSidebarNavigation();
                setupThemeToggle();
                setupSearchFilter();
                }
              });
          }
          // If Dashboard, let it reload the page as normal
        });
      });
    }
  
    // Function to load data based on the current page
    function loadPageData(href) {
      if (href.includes('users.html')) {
        loadUsersFromDB();
      } else if (href.includes('knowledge.html')) {
        loadKnowledgeData();
      } else if (href.includes('messeges.html')) {
        loadMessagesData();
      } else if (href.includes('index.html') || href === 'index.html') {
        updateDashboardCounts();
      }
    }

  
 // Users management functions
// Fetch CSRF token from the meta tag (if it exists)
let csrfToken = null;
const csrfMeta = document.querySelector('meta[name="csrf-token"]');
if (csrfMeta) {
  csrfToken = csrfMeta.getAttribute('content');
  console.log("CSRF token loaded:", csrfToken);
} else {
  console.log("No CSRF token meta tag found");
}


// Now you can use this csrfToken in your fetch request
async function loadUsersFromDB() {
  console.log('Loading users from database...');
  
  const usersTableBody = document.getElementById('users-table-body');
  if (!usersTableBody) {
    console.error('Users table body not found');
    return;
  }

  try {
    const res = await fetch('http://localhost/BayanAI/api/getUsers.php', {
      credentials: 'include',
    });

    console.log('Response status:', res.status);
    const users = await res.json();
    console.log('Users received:', users);

    if (!Array.isArray(users)) {
      console.error("Expected an array, got:", users);
      usersTableBody.innerHTML = `
        <tr><td colspan="6" style="text-align:center;color:red;">
          Failed to load users: ${users.error || 'Unexpected response'}
        </td></tr>`;
      return;
    }

    usersTableBody.innerHTML = ''; // clear existing rows

    users.forEach(user => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${user.id}</td>
        <td>${user.username}</td>
        <td>${user.email}</td>
        <td>${user.department}</td>
        <td>${user.role}</td>
        <td>
          <button class="icon-button flag-action" title="Flag">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
              viewBox="0 0 24 24" fill="none" stroke="currentColor" 
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 22V4a2 2 0 0 1 2-2h11.5a1.5 1.5 0 0 1 1.4 2.1L17 7l1.9 4.9A1.5 1.5 0 0 1 17.5 15H6a2 2 0 0 1 2-2z"/>
            </svg>
          </button>
          <button class="icon-button delete-action" title="Delete">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
              viewBox="0 0 24 24" fill="none" stroke="currentColor" 
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"/>
              <path d="M19 6v14a2 2 0 0 1 2-2H7a2 2 0 0 1 2-2V6m3 0V4
                       a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
              <line x1="10" y1="11" x2="10" y2="17"/>
              <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
          </button>
        </td>
      `;
      usersTableBody.appendChild(row);
    });

    console.log('Users loaded successfully:', users.length);

  } catch (err) {
    console.error("Failed to load users:", err);
    usersTableBody.innerHTML = `
      <tr><td colspan="6" style="text-align:center;color:red;">
        Error loading users
      </td></tr>`;
  }
}

    // Function to load messages data
    async function loadMessagesData() {
      try {
        // This would load messages data when implemented
        console.log('Loading messages data...');
      } catch (err) {
        console.error('Failed to load messages data:', err);
      }
    }
    
    // Function to load knowledge data
    async function loadKnowledgeData() {
      try {
        // This would load knowledge data when implemented
        console.log('Loading knowledge data...');
      } catch (err) {
        console.error('Failed to load knowledge data:', err);
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
      logoutBtn.addEventListener('click', async function(e) {
        e.preventDefault();
        const res = await fetch("http://localhost/BayanAI/api/users/logout.php", {
          method: "POST",
          credentials: "include",
        });
        if (res.ok) {
          window.location.href = "/BayanAI/auth/login/"
        }
        // window.location.href = '/auth/login/index.html'; // Uncomment for real logout
      });
    }
  
    setupSidebarNavigation();
    setupThemeToggle();
    setupSearchFilter();
    setupVolumeChartTooltip();
    
    // Load initial data based on current page
    const currentPath = window.location.pathname;
    if (currentPath.includes('users.html')) {
      loadUsersFromDB();
    } else if (currentPath.includes('knowledge.html')) {
      loadKnowledgeData();
    } else if (currentPath.includes('messeges.html')) {
      loadMessagesData();
    } else if (currentPath.includes('index.html') || currentPath.endsWith('/user-page/')) {
      updateDashboardCounts();
    }
    
    // Load users and setup delete functionality
    loadUsersFromDB();
    setupDeleteFunctionality();
  });
  
  // Also call after SPA navigation:
  function afterSpaNav() {
    setupSidebarNavigation();
    setupThemeToggle();
    setupSearchFilter(); // ✅ reattach search
    setupVolumeChartTooltip();
  
    // Load users and setup delete functionality after SPA navigation
    loadUsersFromDB();
    setupDeleteFunctionality();
  }
  // Patch SPA nav to call afterSpaNav
  (function patchSpaNav() {
    const orig = window.setupSidebarNavigation;
    if (orig) {
      window.setupSidebarNavigation = function() {
        orig();
        setTimeout(afterSpaNav, 0);
      };
    }
  })();
  
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
  
  // If SPA navigation reloads content, re-setup actions
  window.afterSpaNav = function() {
    if (typeof setupInteractionActions === 'function') setupInteractionActions();
    if (typeof setupFlaggedFilterButtons === 'function') setupFlaggedFilterButtons();
  };
  
  // Optional: style flagged rows
  const style = document.createElement('style');
  style.textContent = `.interactions-table tr.flagged { background: rgba(245, 158, 66, 0.12); }`;
  document.head.appendChild(style);
  
  function updateDashboardCounts() {
    // Only run on dashboard page
    if (!document.querySelector('.summary-cards')) return;
    // Users
    const usersTable = document.getElementById('users-table-body');
    const usersCount = usersTable ? usersTable.querySelectorAll('tr').length : null;
    const usersCard = document.getElementById('dashboard-total-users');
    if (usersCount !== null && usersCard) {
      usersCard.textContent = usersCount;
    }
    // Knowledge Base
    const kbTable = document.getElementById('kb-table-body');
    const kbCount = kbTable ? kbTable.querySelectorAll('tr').length : null;
    const kbCard = document.getElementById('dashboard-knowledge-entries');
    if (kbCount !== null && kbCard) {
      kbCard.textContent = kbCount;
    }
  }

  // Call on initial load if on dashboard
  if (window.location.pathname.endsWith('index.html') || window.location.pathname === '/' || window.location.pathname === '') {
    document.addEventListener('DOMContentLoaded', updateDashboardCounts);
  }

  // Observe table changes for live updates (only on dashboard)
  function observeTableCount(tableId, callback) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const observer = new MutationObserver(callback);
    observer.observe(table, { childList: true, subtree: false });
  }
  if (document.querySelector('.summary-cards')) {
    observeTableCount('users-table-body', updateDashboardCounts);
    observeTableCount('kb-table-body', updateDashboardCounts);
  }


// Function to refresh the table
async function refreshUsersTable() {
  await loadUsersFromDB();
}

// Setup delete functionality
function setupDeleteFunctionality() {
  const usersTableBody = document.getElementById('users-table-body');
  if (usersTableBody) {
    usersTableBody.addEventListener('click', async function (e) {
      if (e.target.closest('.delete-action')) {
        const row = e.target.closest('tr');
        row.setAttribute("data-user-id", user.id);
        const userId = row.dataset.userId;

        const confirmed = confirm("Are you sure you want to delete this user?");
        if (!confirmed) return;

        try {
          const res = await fetch('http://localhost/BayanAI/api/deleteUser.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ id: userId }),

          });
          const result = await res.json();

          if (result.success) {
            await refreshUsersTable(); // Refresh the entire table
            console.log("User deleted from DB");
          } else {
            alert("Failed to delete user: " + (result.error || 'Unknown error'));
          }
        } catch (err) {
          console.error("Error deleting user:", err);
        }
      }
    });
  }
}
