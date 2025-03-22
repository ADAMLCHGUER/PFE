// resources/js/layouts/PrestataireLayout.js
import React from 'react';
import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const PrestataireLayout = () => {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        const success = await logout();
        if (success) {
            navigate('/');
        }
    };

    return (
        <div className="d-flex flex-column min-vh-100">
            {/* Header */}
            <header className="bg-dark text-white py-3">
                <div className="container">
                    <div className="row align-items-center">
                        <div className="col-md-6">
                            <Link to="/prestataire" className="text-white text-decoration-none">
                                <h1 className="h4 mb-0">TourismConnect | Espace Prestataire</h1>
                            </Link>
                        </div>
                        <div className="col-md-6">
                            <div className="d-flex justify-content-end">
                                <div className="dropdown">
                                    <button className="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        {user?.name || 'Prestataire'}
                                    </button>
                                    <ul className="dropdown-menu dropdown-menu-end">
                                        <li><Link className="dropdown-item" to="/prestataire/profile">Mon profil</Link></li>
                                        <li><hr className="dropdown-divider" /></li>
                                        <li><Link className="dropdown-item" to="/">Retour au site</Link></li>
                                        <li><button className="dropdown-item" onClick={handleLogout}>Déconnexion</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {/* Contenu principal avec sidebar */}
            <div className="container-fluid flex-grow-1 py-4">
                <div className="row">
                    {/* Sidebar */}
                    <div className="col-md-3 col-lg-2">
                        <div className="list-group mb-4">
                            <Link to="/prestataire" className="list-group-item list-group-item-action" end="true">
                                <i className="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </Link>
                            <Link to="/prestataire/profile" className="list-group-item list-group-item-action">
                                <i className="fas fa-user me-2"></i> Profil du service
                            </Link>
                            <Link to="/prestataire/offers" className="list-group-item list-group-item-action">
                                <i className="fas fa-tags me-2"></i> Gestion des offres
                            </Link>
                            <Link to="/prestataire/stats" className="list-group-item list-group-item-action">
                                <i className="fas fa-chart-line me-2"></i> Statistiques
                            </Link>
                            <div className="list-group-item text-bg-info">
                                <small>
                                    <strong>Abonnement:</strong> {user?.service?.subscription_type?.toUpperCase() || 'Basic'}
                                    {user?.service?.subscription_end_date && (
                                        <span> - Expire le: {new Date(user.service.subscription_end_date).toLocaleDateString()}</span>
                                    )}
                                </small>
                            </div>
                        </div>
                    </div>

                    {/* Contenu principal */}
                    <main className="col-md-9 col-lg-10">
                        <Outlet />
                    </main>
                </div>
            </div>

            {/* Footer */}
            <footer className="bg-dark text-white py-3 mt-auto">
                <div className="container">
                    <div className="small text-center">
                        &copy; {new Date().getFullYear()} TourismConnect. Tous droits réservés.
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default PrestataireLayout;