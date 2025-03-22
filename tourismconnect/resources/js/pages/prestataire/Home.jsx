// resources/js/pages/prestataire/Home.js
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../../contexts/AuthContext';

const PrestataireHome = () => {
    const { user } = useAuth();
    const [stats, setStats] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const response = await axios.get('/api/prestataire/dashboard-stats');
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
            <h1 className="mb-4">Bienvenue, {user?.name}</h1>
            
            {stats && (
                <div className="row">
                    <div className="col-md-3 mb-4">
                        <div className="card bg-primary text-white">
                            <div className="card-body">
                                <h5 className="card-title">Vues (30j)</h5>
                                <p className="card-text h2">{stats.views_count_30days}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-success text-white">
                            <div className="card-body">
                                <h5 className="card-title">Note moyenne</h5>
                                <p className="card-text h2">{stats.average_rating ? stats.average_rating.toFixed(1) : 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-info text-white">
                            <div className="card-body">
                                <h5 className="card-title">Offres actives</h5>
                                <p className="card-text h2">{stats.active_offers_count}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div className="col-md-3 mb-4">
                        <div className="card bg-warning text-white">
                            <div className="card-body">
                                <h5 className="card-title">Avis reçus</h5>
                                <p className="card-text h2">{stats.reviews_count}</p>
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
                                <Link to="/prestataire/profile" className="list-group-item list-group-item-action">
                                    <i className="fas fa-user me-2"></i> Modifier mon profil
                                </Link>
                                <Link to="/prestataire/offers" className="list-group-item list-group-item-action">
                                    <i className="fas fa-tags me-2"></i> Gérer mes offres
                                </Link>
                                <Link to="/prestataire/stats" className="list-group-item list-group-item-action">
                                    <i className="fas fa-chart-line me-2"></i> Voir mes statistiques
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div className="col-md-6 mb-4">
                    <div className="card">
                        <div className="card-header">
                            Informations d'abonnement
                        </div>
                        <div className="card-body">
                            <p><strong>Type:</strong> {user?.service?.subscription_type?.toUpperCase() || 'Basic'}</p>
                            {user?.service?.subscription_end_date && (
                                <p><strong>Expire le:</strong> {new Date(user.service.subscription_end_date).toLocaleDateString()}</p>
                            )}
                            <hr />
                            <Link to="/prestataire/subscription" className="btn btn-primary">
                                Gérer mon abonnement
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PrestataireHome;