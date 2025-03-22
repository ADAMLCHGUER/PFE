// resources/js/pages/auth/PrestataireRegister.js
import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useAuth } from '../../contexts/AuthContext';

const PrestataireRegister = () => {
    const navigate = useNavigate();
    const { user } = useAuth();
    
    const [step, setStep] = useState(1);
    const [categories, setCategories] = useState([]);
    const [cities, setCities] = useState([]);
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');
    
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        service_type: '',
        city_id: '',
        contact: '',
        description: '',
        address: ''
    });
    
    useEffect(() => {
        if (user) {
            navigate('/');
        }
        
        // Charger les catégories et les villes
        const fetchData = async () => {
            try {
                const [categoriesRes, citiesRes] = await Promise.all([
                    axios.get('/api/categories'),
                    axios.get('/api/cities')
                ]);
                
                setCategories(categoriesRes.data);
                setCities(citiesRes.data);
            } catch (error) {
                console.error('Erreur:', error);
            }
        };
        
        fetchData();
    }, [user, navigate]);
    
    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };
    
    const nextStep = () => {
        setStep(2);
    };
    
    const prevStep = () => {
        setStep(1);
    };
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        
        try {
            await axios.post('/register/prestataire', formData);
            setSuccess(true);
        } catch (err) {
            setError(err.response?.data?.message || 'Erreur lors de l\'inscription');
        } finally {
            setLoading(false);
        }
    };
    
    if (success) {
        return (
            <div className="row justify-content-center">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-body text-center">
                            <h3 className="mb-4 text-success">Demande envoyée avec succès!</h3>
                            <p>Votre demande d'inscription en tant que prestataire a été enregistrée.</p>
                            <p>Un administrateur va examiner votre profil et vous recevrez un email dès que votre compte sera activé.</p>
                            <Link to="/" className="btn btn-primary mt-3">Retour à l'accueil</Link>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
    
    return (
        <div className="row justify-content-center">
            <div className="col-md-8">
                <div className="card">
                    <div className="card-header">Devenir prestataire</div>
                    <div className="card-body">
                        {error && <div className="alert alert-danger">{error}</div>}
                        
                        <div className="progress mb-4">
                            <div 
                                className="progress-bar" 
                                role="progressbar" 
                                style={{ width: step === 1 ? '50%' : '100%' }}
                                aria-valuenow={step === 1 ? 50 : 100}
                                aria-valuemin="0" 
                                aria-valuemax="100"
                            ></div>
                        </div>
                        
                        <form onSubmit={handleSubmit}>
                            {step === 1 ? (
                                <>
                                    <h4 className="mb-3">Informations personnelles</h4>
                                    <div className="mb-3">
                                        <label htmlFor="name" className="form-label">Nom de l'entreprise</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            id="name"
                                            name="name"
                                            value={formData.name}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="email" className="form-label">Email</label>
                                        <input
                                            type="email"
                                            className="form-control"
                                            id="email"
                                            name="email"
                                            value={formData.email}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="password" className="form-label">Mot de passe</label>
                                        <input
                                            type="password"
                                            className="form-control"
                                            id="password"
                                            name="password"
                                            value={formData.password}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="password_confirmation" className="form-label">Confirmer le mot de passe</label>
                                        <input
                                            type="password"
                                            className="form-control"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            value={formData.password_confirmation}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="contact" className="form-label">Numéro de contact</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            id="contact"
                                            name="contact"
                                            value={formData.contact}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="d-grid">
                                        <button type="button" className="btn btn-primary" onClick={nextStep}>
                                            Suivant <i className="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </>
                            ) : (
                                <>
                                    <h4 className="mb-3">Informations du service</h4>
                                    <div className="mb-3">
                                        <label htmlFor="service_type" className="form-label">Type de service</label>
                                        <select
                                            className="form-select"
                                            id="service_type"
                                            name="service_type"
                                            value={formData.service_type}
                                            onChange={handleChange}
                                            required
                                        >
                                            <option value="">Sélectionnez un type</option>
                                            {categories.map(category => (
                                                <option key={category.id} value={category.id}>
                                                    {category.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="city_id" className="form-label">Ville</label>
                                        <select
                                            className="form-select"
                                            id="city_id"
                                            name="city_id"
                                            value={formData.city_id}
                                            onChange={handleChange}
                                            required
                                        >
                                            <option value="">Sélectionnez une ville</option>
                                            {cities.map(city => (
                                                <option key={city.id} value={city.id}>
                                                    {city.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="description" className="form-label">Description</label>
                                        <textarea
                                            className="form-control"
                                            id="description"
                                            name="description"
                                            rows="3"
                                            value={formData.description}
                                            onChange={handleChange}
                                            required
                                        ></textarea>
                                    </div>
                                    
                                    <div className="mb-3">
                                        <label htmlFor="address" className="form-label">Adresse</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            id="address"
                                            name="address"
                                            value={formData.address}
                                            onChange={handleChange}
                                            required
                                        />
                                    </div>
                                    
                                    <div className="d-flex gap-2">
                                        <button type="button" className="btn btn-secondary" onClick={prevStep}>
                                            <i className="fas fa-arrow-left"></i> Précédent
                                        </button>
                                        <button type="submit" className="btn btn-primary" disabled={loading}>
                                            {loading ? 'Inscription en cours...' : 'S\'inscrire'}
                                        </button>
                                    </div>
                                </>
                            )}
                        </form>
                        
                        <div className="mt-3">
                            <p>Déjà inscrit? <Link to="/login">Se connecter</Link></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PrestataireRegister;