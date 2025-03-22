// resources/js/pages/auth/Login.js
import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Login = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const { user, login, error: authError } = useAuth();
    
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        remember: false
    });
    
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    
    // Rediriger si déjà connecté
    useEffect(() => {
        if (user) {
            navigate('/');
        }
    }, [user, navigate]);
    
    // Récupérer les paramètres de redirection
    useEffect(() => {
        const params = new URLSearchParams(location.search);
        const message = params.get('message');
        if (message) {
            setError(message);
        }
    }, [location]);
    
    const handleChange = (e) => {
        const { name, value, checked, type } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value
        }));
        
        // Effacer les erreurs
        setError('');
    };
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        
        try {
            const success = await login(formData.email, formData.password);
            
            if (success) {
                // Rediriger vers la page d'accueil ou la page précédente
                const params = new URLSearchParams(location.search);
                const redirect = params.get('redirect');
                navigate(redirect || '/');
            } else {
                setError(authError || 'Une erreur est survenue lors de la connexion.');
            }
        } catch (err) {
            setError('Les identifiants fournis ne correspondent pas à nos enregistrements.');
        } finally {
            setLoading(false);
        }
    };
    
    return (
        <div className="row justify-content-center">
            <div className="col-md-8 col-lg-6">
                <div className="card shadow">
                    <div className="card-body p-5">
                        <h2 className="text-center mb-4">Connexion</h2>
                        
                        {error && (
                            <div className="alert alert-danger" role="alert">
                                {error}
                            </div>
                        )}
                        
                        <form onSubmit={handleSubmit}>
                            <div className="mb-3">
                                <label htmlFor="email" className="form-label">Adresse email</label>
                                <input
                                    type="email"
                                    className="form-control"
                                    id="email"
                                    name="email"
                                    value={formData.email}
                                    onChange={handleChange}
                                    required
                                    autoFocus
                                />
                            </div>
                            
                            <div className="mb-3">
                                <div className="d-flex justify-content-between">
                                    <label htmlFor="password" className="form-label">Mot de passe</label>
                                    <Link to="/forgot-password" className="text-decoration-none small">
                                        Mot de passe oublié ?
                                    </Link>
                                </div>
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
                            
                            <div className="mb-3 form-check">
                                <input
                                    type="checkbox"
                                    className="form-check-input"
                                    id="remember"
                                    name="remember"
                                    checked={formData.remember}
                                    onChange={handleChange}
                                />
                                <label className="form-check-label" htmlFor="remember">
                                    Se souvenir de moi
                                </label>
                            </div>
                            
                            <div className="d-grid">
                                <button
                                    type="submit"
                                    className="btn btn-primary btn-lg"
                                    disabled={loading}
                                >
                                    {loading ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Connexion en cours...
                                        </>
                                    ) : (
                                        <>Se connecter</>
                                    )}
                                </button>
                            </div>
                        </form>
                        
                        <div className="mt-4 text-center">
                            <p>
                                Vous n'avez pas de compte ? <Link to="/register">S'inscrire</Link>
                            </p>
                            <p>
                                Vous êtes un prestataire ? <Link to="/prestataire/register">Devenir prestataire</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Login;