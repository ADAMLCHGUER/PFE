// resources/js/pages/Home.js
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const Home = () => {
    const [featuredServices, setFeaturedServices] = useState([]);
    const [categories, setCategories] = useState([]);
    const [cities, setCities] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [searchParams, setSearchParams] = useState({
        keyword: '',
        category_id: '',
        city_id: ''
    });

    useEffect(() => {
        const fetchData = async () => {
            try {
                const [servicesRes, categoriesRes, citiesRes] = await Promise.all([
                    axios.get('/api/services/featured'),
                    axios.get('/api/categories'),
                    axios.get('/api/cities')
                ]);
                
                setFeaturedServices(servicesRes.data);
                setCategories(categoriesRes.data);
                setCities(citiesRes.data);
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
                setError('Impossible de charger les données. Veuillez réessayer plus tard.');
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    const handleSearchChange = (e) => {
        const { name, value } = e.target;
        setSearchParams(prev => ({ ...prev, [name]: value }));
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        
        // Construire l'URL avec les paramètres de recherche
        const searchQuery = new URLSearchParams();
        
        if (searchParams.keyword) {
            searchQuery.append('keyword', searchParams.keyword);
        }
        
        if (searchParams.category_id) {
            searchQuery.append('category_id', searchParams.category_id);
        }
        
        if (searchParams.city_id) {
            searchQuery.append('city_id', searchParams.city_id);
        }
        
        // Rediriger vers la page de recherche avec les paramètres
        window.location.href = `/services?${searchQuery.toString()}`;
    };

    return (
        <div>
            {/* Hero section avec formulaire de recherche */}
            <section className="py-5 text-center bg-light rounded mb-5">
                <div className="container">
                    <h1 className="display-5 fw-bold mb-4">Trouvez les meilleurs services touristiques</h1>
                    <p className="lead mb-4">
                        Hôtels, restaurants, activités et plus encore. Tout ce dont vous avez besoin pour un séjour parfait.
                    </p>
                    
                    <div className="row justify-content-center">
                        <div className="col-md-8">
                            <form onSubmit={handleSearchSubmit} className="p-4 bg-white shadow rounded">
                                <div className="row g-3">
                                    <div className="col-12">
                                        <input
                                            type="text"
                                            className="form-control form-control-lg"
                                            placeholder="Que recherchez-vous ?"
                                            name="keyword"
                                            value={searchParams.keyword}
                                            onChange={handleSearchChange}
                                        />
                                    </div>
                                    
                                    <div className="col-md-6">
                                        <select
                                            className="form-select form-select-lg"
                                            name="category_id"
                                            value={searchParams.category_id}
                                            onChange={handleSearchChange}
                                        >
                                            <option value="">Toutes les catégories</option>
                                            {categories.map(category => (
                                                <option key={category.id} value={category.id}>
                                                    {category.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    
                                    <div className="col-md-6">
                                        <select
                                            className="form-select form-select-lg"
                                            name="city_id"
                                            value={searchParams.city_id}
                                            onChange={handleSearchChange}
                                        >
                                            <option value="">Toutes les villes</option>
                                            {cities.map(city => (
                                                <option key={city.id} value={city.id}>
                                                    {city.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    
                                    <div className="col-12">
                                        <button type="submit" className="btn btn-primary btn-lg w-100">
                                            <i className="fas fa-search me-2"></i> Rechercher
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            
            {/* Section des services mis en avant */}
            <section className="mb-5">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <h2 className="h3 mb-0">Services en vedette</h2>
                    <Link to="/services" className="btn btn-outline-primary">
                        Voir tous les services <i className="fas fa-arrow-right ms-1"></i>
                    </Link>
                </div>
                
                {loading ? (
                    <div className="text-center py-4">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                ) : error ? (
                    <div className="alert alert-danger" role="alert">
                        {error}
                    </div>
                ) : featuredServices.length === 0 ? (
                    <div className="alert alert-info" role="alert">
                        Aucun service en vedette pour le moment.
                    </div>
                ) : (
                    <div className="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        {featuredServices.map(service => (
                            <div key={service.id} className="col">
                                <div className="card h-100">
                                    <img 
                                        src={service.main_image?.image_path || '/img/placeholder.jpg'} 
                                        className="card-img-top" 
                                        alt={service.name}
                                        style={{ height: '200px', objectFit: 'cover' }}
                                    />
                                    
                                    <div className="card-body">
                                        <div className="d-flex justify-content-between mb-2">
                                            <span className="badge bg-primary">{service.category.name}</span>
                                            <span className="badge bg-secondary">{service.city.name}</span>
                                        </div>
                                        
                                        <h5 className="card-title">{service.name}</h5>
                                        <p className="card-text text-muted small">
                                            {service.description.length > 100 
                                                ? `${service.description.substring(0, 100)}...` 
                                                : service.description
                                            }
                                        </p>
                                        
                                        {service.avg_rating && (
                                            <div className="mb-2">
                                                {[...Array(5)].map((_, index) => (
                                                    <i 
                                                        key={index}
                                                        className={`fas fa-star ${index < Math.round(service.avg_rating) ? 'text-warning' : 'text-muted'}`}
                                                        style={{fontSize: '0.8rem'}}
                                                    ></i>
                                                ))}
                                                <span className="ms-2 text-muted small">
                                                    ({service.reviews_count} avis)
                                                </span>
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="card-footer bg-white">
                                        <Link to={`/services/${service.id}`} className="btn btn-outline-primary w-100">
                                            Voir les détails
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </section>
            
            {/* Section pour les prestataires */}
            <section className="bg-light p-5 rounded text-center mb-5">
                <h2 className="h3 mb-3">Vous êtes un prestataire de services touristiques ?</h2>
                <p className="mb-4">
                    Rejoignez notre plateforme et augmentez votre visibilité auprès des touristes.
                </p>
                <Link to="/prestataire/register" className="btn btn-primary btn-lg">
                    Devenir prestataire
                </Link>
            </section>
        </div>
    );
};

export default Home;