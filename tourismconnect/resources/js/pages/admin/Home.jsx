// resources/js/pages/admin/Home.js
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const AdminHome = () => {
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const response = await axios.get('/api/admin/dashboard-stats');
                setStats(response.data);
            } catch (err) {
                setError('Impossible de charger les statistiques');
                console.error(err);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, []);

    if (loading) {
        return <div className="text-center py-5">Chargement...</div>;
    }

    if (error) {
        return <div className="alert alert-danger">{error}</div>;
    }

    return (
        <div>
            <h1 className="mb-4">Tableau de bord administrateur</h1>
            
            {stats && (
                <div className="row">
                    <div className="col-md-3 mb-4">
                        <div className="card bg-primary text-white">
                            <div className="card-body">
                                <h5 className="card-title">Utilisateurs</h5>
                                <p className="card-text h2">{stats.total_users}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-success text-white">
                            <div className="card-body">
                                <h5 className="card-title">Services</h5>
                                <p className="card-text h2">{stats.total_services}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-info text-white">
                            <div className="card-body">
                                <h5 className="card-title">Avis</h5>
                                <p className="card-text h2">{stats.total_reviews}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-warning text-white">
                            <div className="card-body">
                                <h5 className="card-title">En attente</h5>
                                <p className="card-text h2">{stats.pending_verifications}</p>
                            </div>
                        </div>
                    </div>
                </div>
            )}
            
            <div className="row mt-4">
                <div className="col-md-6 mb-4">
                    <div className="card">
                        <div className="card-header">
                            Actions rapides
                        </div>
                        <div className="card-body">
                            <div className="list-group">
                                <Link to="/admin/verifications" className="list-group-item list-group-item-action">
                                    <i className="fas fa-user-check me-2"></i> Vérifier les prestataires en attente
                                </Link>
                                <Link to="/admin/users" className="list-group-item list-group-item-action">
                                    <i className="fas fa-users me-2"></i> Gérer les utilisateurs
                                </Link>
                                <Link to="/admin/categories" className="list-group-item list-group-item-action">
                                    <i className="fas fa-list me-2"></i> Gérer les catégories
                                </Link>
                                <Link to="/admin/cities" className="list-group-item list-group-item-action">
                                    <i className="fas fa-city me-2"></i> Gérer les villes
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div className="col-md-6 mb-4">
                    <div className="card">
                        <div className="card-header">
                            Activité récente
                        </div>
                        <div className="card-body">
                            <p>Affichage des activités récentes à venir...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AdminHome;