document.addEventListener('DOMContentLoaded', () => {
    const config = window.impersonate.config;
    const root = document.querySelector('.lp-root');
    if (!root) return;

    const toggle = root.querySelector('.lp-toggle');
    const container = root.querySelector('.lp-container');
    const selectTrigger = root.querySelector('.lp-select-trigger');
    const dropdown = root.querySelector('.lp-dropdown');
    const searchInput = root.querySelector('.lp-search-input');
    const resultsContainer = root.querySelector('.lp-results');
    const leaveBtn = root.querySelector('.lp-logout a');
    
    let debounceTimer;

    // Toggle main panel
    if (toggle) {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            container.classList.toggle('lp-open');
        });
    }

    // Toggle dropdown (select UI)
    if (selectTrigger) {
        selectTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = dropdown.classList.contains('lp-show');
            
            if (isOpen) {
                dropdown.classList.remove('lp-show');
                selectTrigger.classList.remove('lp-active');
            } else {
                dropdown.classList.add('lp-show');
                selectTrigger.classList.add('lp-active');
                
                adjustDropdownPosition();
                
                searchInput.focus();
                
                // Fetch default users if empty
                if (resultsContainer.children.length === 0) {
                    fetchUsers('');
                }
            }
        });
    }

    function adjustDropdownPosition() {
        if (!dropdown || !dropdown.classList.contains('lp-show')) return;
        
        // Auto position: drop up if no space below
        dropdown.style.top = 'calc(100% + 4px)';
        dropdown.style.bottom = 'auto';
        
        const rect = dropdown.getBoundingClientRect();
        if (rect.bottom > window.innerHeight) {
            dropdown.style.top = 'auto';
            dropdown.style.bottom = 'calc(100% + 4px)';
        }
    }

    // Close things when clicking outside
    document.addEventListener('click', (e) => {
        // Close dropdown
        if (dropdown && dropdown.classList.contains('lp-show')) {
            if (!selectTrigger.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('lp-show');
                selectTrigger.classList.remove('lp-active');
            }
        }
        
        // Close panel
        if (container && container.classList.contains('lp-open')) {
            if (!root.contains(e.target)) {
                container.classList.remove('lp-open');
                if (dropdown) {
                    dropdown.classList.remove('lp-show');
                    selectTrigger.classList.remove('lp-active');
                }
            }
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
    }

    function fetchUsers(query) {
        resultsContainer.innerHTML = '<div class="lp-loading">Searching...</div>';

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
            resultsContainer.innerHTML = '<div class="lp-empty">Error fetching users</div>';
        });
    }

    function renderResults(users) {
        if (!users || users.length === 0) {
            resultsContainer.innerHTML = '<div class="lp-empty">No users found</div>';
            adjustDropdownPosition();
            return;
        }

        resultsContainer.innerHTML = '';
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'lp-result-item';
            
            item.innerHTML = `<span class="lp-result-name">${user.val}</span>`;
            
            item.addEventListener('click', () => {
                impersonateUser(user.key);
            });
            resultsContainer.appendChild(item);
        });
        
        adjustDropdownPosition();
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
