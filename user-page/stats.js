/**
 * Dashboard Statistics Handler
 * Fetches and displays real-time statistics for the user dashboard
 */
class DashboardStats {
    constructor() {
        // Use absolute URL to avoid relative path issues
        this.apiUrl = 'http://localhost/BayanAI/api/userStats.php';
        this.statsData = null;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.showLoadingState();
        this.loadStats();
        setInterval(() => this.loadStats(), 5 * 60 * 1000);

    }

    showLoadingState() {
        const cards = ['users', 'chats', 'knowledge-entries', 'sessions'];
        cards.forEach(cardId => {
            const element = document.getElementById(`dashboard-total-${cardId}`);
            const cardElement = element?.closest('.dashboard-card');
            if (element) {
                element.textContent = 'Loading...';
                element.style.opacity = '0.7';
            }
            if (cardElement) {
                cardElement.classList.add('loading');
            }
        });
    }

    async loadStats() {
        if (this.isLoading) return;

        this.isLoading = true;
        this.clearErrorStates();

        try {
            const response = await fetch(this.apiUrl, {
                method: 'GET',
                credentials: 'include'
            });

            if (!response.ok) throw new Error(`HTTP error ${response.status}`);

            const data = await response.json();

            if (data.success) {
                this.statsData = data;
                this.updateDashboard();
                this.hideLoadingState();
            } else {
                this.showErrorState(data.error || 'Failed to load');
            }
        } catch (error) {
            console.error('Error loading dashboard stats:', error);
            this.showErrorState('Network error');
        } finally {
            this.isLoading = false;
        }
    }

    hideLoadingState() {
        const cards = ['users', 'chats', 'knowledge-entries', 'sessions'];
        cards.forEach(cardId => {
            const element = document.getElementById(`dashboard-total-${cardId}`);
            const cardElement = element?.closest('.dashboard-card');
            if (element) element.style.opacity = '1';
            if (cardElement) cardElement.classList.remove('loading');
        });
    }

    clearErrorStates() {
        const cards = ['users', 'chats', 'knowledge-entries', 'sessions'];
        cards.forEach(cardId => {
            const element = document.getElementById(`dashboard-total-${cardId}`);
            const cardElement = element?.closest('.dashboard-card');
            if (element) element.style.color = '';
            if (cardElement) cardElement.classList.remove('error');
        });
    }

    setupRefreshButton() {
        const refreshButton = document.getElementById('refresh-stats');
        if (refreshButton) {
            refreshButton.addEventListener('click', () => {
                this.loadStats();
                refreshButton.style.transform = 'rotate(360deg)';
                setTimeout(() => { refreshButton.style.transform = 'rotate(0deg)'; }, 500);
            });
        }
    }

    updateDashboard() {
        if (!this.statsData) return;

        this.updateCard('users', this.statsData.users);
        this.updateCard('chats', this.statsData.chats);
        this.updateCard('knowledge-entries', this.statsData.knowledge_base);
        this.updateSessionsCard();
    }

    updateCard(type, data) {
        const valueElement = document.getElementById(`dashboard-total-${type}`);
        const changeElement = document.getElementById(`dashboard-${type}-change`);
        const percentElement = document.getElementById(`dashboard-${type}-percent`);

        if (valueElement && data.total !== undefined) {
            valueElement.textContent = this.formatNumber(data.total);
        }
        if (changeElement && percentElement && data.change_percent !== undefined) {
            percentElement.textContent = `${Math.abs(data.change_percent)}%`;
            this.updateChangeDirection(changeElement, data.change_direction);
        }
    }

    updateSessionsCard() {
        const valueElement = document.getElementById('dashboard-total-sessions');
        if (valueElement && this.statsData.sessions.total !== undefined) {
            valueElement.textContent = this.formatNumber(this.statsData.sessions.total);
        }
        const changeElement = document.getElementById('dashboard-sessions-change');
        const percentElement = document.getElementById('dashboard-sessions-percent');
        if (changeElement) changeElement.style.display = 'none';
        if (percentElement) percentElement.style.display = 'none';
    }

    updateChangeDirection(changeElement, direction) {
        changeElement.classList.remove('increase', 'decrease');
        changeElement.classList.add(direction);

        const arrowIcon = changeElement.querySelector('svg');
        if (arrowIcon) {
            if (direction === 'increase') {
                arrowIcon.innerHTML = '<path d="M7 17h10V7"/><path d="M7 17 17 7"/>';
            } else {
                arrowIcon.innerHTML = '<path d="M7 7h10v10"/><path d="M17 7 7 17"/>';
            }
        }
    }

    formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    showErrorState(message = 'Failed to load') {
        const cards = ['users', 'chats', 'knowledge-entries', 'sessions'];
        cards.forEach(cardId => {
            const element = document.getElementById(`dashboard-total-${cardId}`);
            const cardElement = element?.closest('.dashboard-card');
            if (element) {
                element.textContent = 'Error';
                element.style.color = '#ef4444';
                element.style.opacity = '1';
            }
            if (cardElement) {
                cardElement.classList.remove('loading');
                cardElement.classList.add('error');
            }
        });
        console.error('Dashboard stats error:', message);
    }
}

document.addEventListener('DOMContentLoaded', () => new DashboardStats());
