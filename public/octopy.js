document.addEventListener('DOMContentLoaded', () => {
    const config = window.impersonate.config;
    const root = document.querySelector('.oim-root');
    if (!root) return;

    const toggle = root.querySelector('.oim-toggle');
    const container = root.querySelector('.oim-container');
    const searchInput = root.querySelector('.oim-search-input');
    const resultsContainer = root.querySelector('.oim-results');
    const leaveBtn = root.querySelector('.oim-logout a');
    
    let debounceTimer;

    // Toggle panel
    if (toggle) {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.toggle('oim-open');
            if (container.classList.contains('oim-open') && searchInput) {
                searchInput.focus();
            }
        });
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!root.contains(e.target) && container.classList.contains('oim-open')) {
            container.classList.remove('oim-open');
            if (resultsContainer) resultsContainer.classList.remove('oim-show');
        }
    });

    // Handle impersonation leave
    if (leaveBtn) {
        leaveBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fetch(config.route + '/_impersonate/leave', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.token,
                    'Accept': 'application/json',
                }
            }).then(() => {
                window.location.reload();
            });
        });
    }

    // Handle search input
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            
            clearTimeout(debounceTimer);
            
            debounceTimer = setTimeout(() => {
                fetchUsers(query);
            }, config.delay || 250);
        });

        // Hide results when clicking outside search area
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.remove('oim-show');
            }
        });
        
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim().length === 0 && resultsContainer.children.length === 0) {
                fetchUsers('');
            } else if (resultsContainer.children.length > 0) {
                resultsContainer.classList.add('oim-show');
            }
        });
    }

    function fetchUsers(query) {
        resultsContainer.innerHTML = '<div class="oim-loading">Searching...</div>';
        resultsContainer.classList.add('oim-show');

        fetch(`${config.route}/_impersonate/users?query=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            renderResults(data.data);
        })
        .catch(() => {
            resultsContainer.innerHTML = '<div class="oim-empty">Error fetching users</div>';
        });
    }

    function renderResults(users) {
        if (!users || users.length === 0) {
            resultsContainer.innerHTML = '<div class="oim-empty">No users found</div>';
            return;
        }

        resultsContainer.innerHTML = '';
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'oim-result-item';
            
            item.innerHTML = `<span class="oim-result-name">${user.val}</span>`;
            
            item.addEventListener('click', () => {
                impersonateUser(user.key);
            });
            resultsContainer.appendChild(item);
        });
    }

    function impersonateUser(id) {
        fetch(config.route + '/_impersonate/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ user: id })
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Failed to impersonate user.');
            }
        });
    }
});
