import React, { createContext, useState, useEffect, useContext } from 'react';
import axios from 'axios';

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        // Vérifier si l'utilisateur est déjà connecté
        const checkAuth = async () => {
            try {
                const response = await axios.get('/api/user');
                setUser(response.data);
            } catch (error) {
                setUser(null);
            } finally {
                setLoading(false);
            }
        };

        checkAuth();
    }, []);

    // Fonctions d'authentification
    const login = async (email, password) => {
        try {
            await axios.get('/sanctum/csrf-cookie');
            
            const response = await axios.post('/login', {
                email,
                password
            });
            
            const userResponse = await axios.get('/api/user');
            setUser(userResponse.data);
            setError(null);
            return true;
        } catch (err) {
            setError(err.response?.data?.message || 'Erreur de connexion');
            return false;
        }
    };

    const logout = async () => {
        try {
            await axios.post('/logout');
            setUser(null);
            return true;
        } catch (err) {
            setError(err.response?.data?.message || 'Erreur de déconnexion');
            return false;
        }
    };

    // Valeur à fournir dans le context
    const value = {
        user,
        loading,
        error,
        login,
        logout,
        hasRole: (role) => {
            if (!user) return false;
            
            if (role === 'admin') {
                return user.type === 'admin';
            } else if (role === 'prestataire') {
                return user.type === 'prestataire' && user.status === 'active';
            } else if (role === 'touriste') {
                return user.type === 'touriste';
            }
            
            return false;
        }
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth doit être utilisé au sein d\'un AuthProvider');
    }
    return context;
};