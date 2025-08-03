@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-indigo-600 font-semibold rounded-t-lg">
                    Admin Menu
                </div>
                <div class="card-body p-0">
                    <nav class="nav flex-column">
                        <a class="nav-link text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 py-3 px-4 rounded-b-lg transition duration-200 ease-in-out active-link"
                           href="#" data-section="overview" data-url="{{ route('Backoffice.partials.overview') }}">
                            Overview
                        </a>

                        @can('manage-users')
                        <a class="nav-link text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 py-3 px-4 rounded-b-lg transition duration-200 ease-in-out"
                           href="#" data-section="users" data-url="{{ route('Backoffice.partials.users') }}">
                            Users
                        </a>
                        @endcan

                        @can('manage-roles')
                        <a class="nav-link text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 py-3 px-4 rounded-b-lg transition duration-200 ease-in-out"
                           href="#" data-section="roles" data-url="">
                            Roles
                        </a>
                        @endcan
                        <a class="nav-link text-danger py-3 px-4 rounded-b-lg transition duration-200 ease-in-out active-link"
                           href="{{ route('home') }}" >
                            Exit
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            <div class="card shadow-sm rounded-lg">
                <div class="card-body" id="admin-content-area">
                    <div class="text-center py-5">
                        <div class="spinner-border text-indigo-600" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-indigo-600">Loading initial content...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Global para Edição -->
<div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="userEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="userEditModalContent">
          
        </div>
    </div>
</div>

<script>
    let userEditModal = null;


    document.addEventListener('DOMContentLoaded', function() {
        userEditModal = new bootstrap.Modal(document.getElementById('userEditModal'));

        document.addEventListener('click', function(e) {
            const editButton = e.target.closest('.edit-user-btn');
            if (!editButton) return;

            e.preventDefault();
            const editUrl = editButton.dataset.editUrl;
            
            if (!editUrl) {
                console.error('Edit URL not found');
                return;
            }

            handleEditClick(editUrl);
        });

        async function handleEditClick(editUrl) {
            if (!userEditModal) {
                console.error('Modal not initialized');
                return;
            }

            const modalContent = document.getElementById('userEditModalContent');
            
            modalContent.innerHTML = `
                <div class="modal-header">
                    <h5 class="modal-title">Carregando...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Carregando dados do utilizador...</p>
                </div>
            `;

            userEditModal.show();

            try {
                const response = await fetch(editUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const html = await response.text();
                modalContent.innerHTML = html;

                const form = modalContent.querySelector('#editUserForm');
                if (form) {
                    form.addEventListener('submit', handleFormSubmit);
                }

            } catch (error) {
                console.error('Error loading data:', error);
                modalContent.innerHTML = `
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Erro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            Não foi possível carregar os dados do utilizador. Por favor, tente novamente.
                        </div>
                    </div>
                `;
            }
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    userEditModal.hide();
                    
                    const usersLink = document.querySelector('.nav-link[data-section="users"]');
                    if (usersLink && usersLink.dataset.url) {
                        delete contentCache[usersLink.dataset.url];
                        
                        try {
                            const response = await fetch(usersLink.dataset.url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Cache-Control': 'no-cache',
                                    'Pragma': 'no-cache'
                                }
                            });
                            
                            if (!response.ok) throw new Error('Network response was not ok');
                            
                            const html = await response.text();
                            const contentArea = document.getElementById('admin-content-area');
                            contentArea.innerHTML = html;
                            
                            const successAlert = document.createElement('div');
                            successAlert.className = 'alert alert-success alert-dismissible fade show';
                            successAlert.innerHTML = `
                                ${data.message || 'User updated successfully!'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            contentArea.insertBefore(successAlert, contentArea.firstChild);
                            
                            setTimeout(() => {
                                successAlert.remove();
                            }, 3000);
                            
                            attachPaginationEventListeners();
                        } catch (error) {
                            console.error('Error reloading content:', error);
                        }
                    }
                } else if (data.errors) {
                    form.querySelectorAll('.text-danger').forEach(el => el.textContent = '');

                    Object.keys(data.errors).forEach(field => {
                        const errorDiv = form.querySelector(`#${field}-error`);
                        if (errorDiv) {
                            errorDiv.textContent = data.errors[field][0];
                        }
                    });
                }
            } catch (error) {
                console.error('Error sending form:', error);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.textContent = 'Error saving changes. Please try again.';
                form.insertBefore(alertDiv, form.firstChild);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save changes';
            }
        }
        const contentArea = document.getElementById('admin-content-area');
        const navLinks = document.querySelectorAll('.nav-link[data-url]');
        const contentCache = {};
        
        async function loadContent(url, targetElement, skipCache = false) {
            if (!skipCache && contentCache[url]) {
                targetElement.innerHTML = contentCache[url];
                console.log('Content loaded from cache:', url);
                attachPaginationEventListeners();
                return;
            }

            targetElement.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-indigo-600" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-indigo-600">Loading content...</p></div>'; // Indicador de carregamento
            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'text/html'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! Status: ${response.status}. Message: ${errorText}`);
                }

                const html = await response.text();
                targetElement.innerHTML = html;
                contentCache[url] = html;
                console.log('Content fetched and cached:', url);

                attachPaginationEventListeners();

            } catch (error) {
                console.error('Error loading content:', error);
                let errorMessage = 'Error loading content.';
                if (error.message.includes('403')) {
                    errorMessage = 'Access Denied, you don\'t have permission to view this.';
                } else if (error.message.includes('404')) {
                    errorMessage = 'Content not found.';
                }
                targetElement.innerHTML = `<div class="alert alert-danger" role="alert">${errorMessage}</div>`;
            }
        }

        function setupEditUserModal() {
            const modalElement = document.getElementById('editUserModal');
            editUserModalContent = document.getElementById('editUserModalContent');
            
            if (editUserModal) {
                editUserModal.dispose();
            }
            editUserModal = new bootstrap.Modal(modalElement);

            contentArea.removeEventListener('click', handleEditUserClick);
            contentArea.addEventListener('click', handleEditUserClick);
            
            document.querySelectorAll('.edit-user-btn').forEach(button => {
                button.addEventListener('click', handleEditUserClick);
            });
        }

        async function handleEditUserClick(e) {
            e.preventDefault();
            const btn = e.target.closest('.edit-user-btn') || e.currentTarget;
            if (!btn || !btn.classList.contains('edit-user-btn')) return;
            console.log('Edit button clicked', btn);
            const editUrl = btn.dataset.editUrl;
            if (!editUserModalContent || !editUserModal) return;
            editUserModalContent.innerHTML = `
                <div class="modal-header">
                    <h5 class="modal-title">Loading...</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Loading user data...</p>
                </div>
            `;
            editUserModal.show();
            try {
                const response = await fetch(editUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'text/html'
                    }
                });
                if (!response.ok) throw new Error(`Error ${response.status}`);
                const html = await response.text();
                editUserModalContent.innerHTML = html;
            } catch (error) {
                editUserModalContent.innerHTML = `
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">Unable to load user data.</div>
                    </div>
                `;
            }
        }

        function attachPaginationEventListeners() {
            const paginationLinks = contentArea.querySelectorAll('.pagination a');

            paginationLinks.forEach(link => {
                link.removeEventListener('click', handlePaginationClick);
                link.addEventListener('click', handlePaginationClick);
            });
        }

        function handlePaginationClick(e) {
            e.preventDefault();
            const url = this.href;
            loadContent(url, contentArea);
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                navLinks.forEach(nav => nav.classList.remove('active-link'));
                this.classList.add('active-link');

                const url = this.dataset.url;
                if (url) {
                    loadContent(url, contentArea);
                }
            });
        });

        const initialLink = document.querySelector('.nav-link[data-section="overview"]');
        if (initialLink) {
            loadContent(initialLink.dataset.url, contentArea);
        }
    });
</script>
@endsection