// resources/js/layouts/MainLayout.js
import React from 'react';
import { Outlet, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const MainLayout = () => {
    const { user, logout, hasRole } = useAuth();
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
            <header className="bg-primary text-white py-3">
                <div className="container">
                    <div className="row align-items-center">
                        <div className="col-md-4">
                            <Link to="/" className="text-white text-decoration-none">
                                <h1 className="h4 mb-0">TourismConnect</h1>
                            </Link>
                        </div>
                        <div className="col-md-8">
                            <nav className="d-flex justify-content-end">
                                <ul className="nav">
                                    <li className="nav-item">
                                        <Link to="/" className="nav-link text-white">Accueil</Link>
                                    </li>
                                    <li className="nav-item">
                                        <Link to="/services" className="nav-link text-white">Services</Link>
                                    </li>
                                    {!user ? (
                                        <>
                                            <li className="nav-item">
                                                <Link to="/login" className="nav-link text-white">Se connecter</Link>
                                            </li>
                                            <li className="nav-item">
                                                <Link to="/register" className="nav-link text-white">S'inscrire</Link>
                                            </li>
                                            <li className="nav-item">
                                                <Link to="/prestataire/register" className="nav-link text-white">
                                                    Devenir prestataire
                                                </Link>
                                            </li>
                                        </>
                                    ) : (
                                        <>
                                            {hasRole('admin') && (
                                                <li className="nav-item">
                                                    <Link to="/admin" className="nav-link text-white">
                                                        Administration
                                                    </Link>
                                                </li>
                                            )}
                                            {hasRole('prestataire') && (
                                                <li className="nav-item">
                                                    <Link to="/prestataire" className="nav-link text-white">
                                                        Espace prestataire
                                                    </Link>
                                                </li>
                                            )}
                                            <li className="nav-item dropdown">
                                                <a 
                                                    className="nav-link dropdown-toggle text-white" 
                                                    href="#" 
                                                    role="button" 
                                                    data-bs-toggle="dropdown"
                                                >
                                                    {user.name}
                                                </a>
                                                <ul className="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <Link to="/profile" className="dropdown-item">
                                                            Mon profil
                                                        </Link>
                                                    </li>
                                                    <li><hr className="dropdown-divider" /></li>
                                                    <li>
                                                        <button 
                                                            className="dropdown-item" 
                                                            onClick={handleLogout}
                                                        >
                                                            Déconnexion
                                                        </button>
                                                    </li>
                                                </ul>
                                            </li>
                                        </>
                                    )}
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </header>

            {/* Contenu principal */}
            <main className="flex-grow-1 py-4">
                <div className="container">
                    <Outlet />
                </div>
            </main>

            {/* Footer */}
            <footer className="bg-dark text-white py-4 mt-auto">
                <div className="container">
                    <div className="row">
                        <div className="col-md-4">
                            <h5>TourismConnect</h5>
                            <p className="small">
                                Plateforme de services touristiques intégrée facilitant la découverte de prestations de qualité.
                            </p>
                        </div>
                        <div className="col-md-4">
                            <h5>Liens utiles</h5>
                            <ul className="list-unstyled small">
                                <li><Link to="/about" className="text-white">À propos</Link></li>
                                <li><Link to="/contact" className="text-white">Contact</Link></li>
                                <li><Link to="/terms" className="text-white">Conditions d'utilisation</Link></li>
                                <li><Link to="/privacy" className="text-white">Politique de confidentialité</Link></li>
                            </ul>
                        </div>
                        <div className="col-md-4">
                            <h5>Contactez-nous</h5>
                            <address className="small">
                                <i className="fas fa-map-marker-alt me-2"></i> 123 Rue du Tourisme<br />
                                <i className="fas fa-phone me-2"></i> +33 1 23 45 67 89<br />
                                <i className="fas fa-envelope me-2"></i> contact@tourismconnect.com
                            </address>
                        </div>
                    </div>
                    <hr className="my-3" />
                    <div className="text-center small">
                        &copy; {new Date().getFullYear()} TourismConnect. Tous droits réservés.
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default MainLayout;