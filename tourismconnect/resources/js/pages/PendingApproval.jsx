// resources/js/pages/PendingApproval.jsx
import React from 'react';
import { Link } from 'react-router-dom';

const PendingApproval = () => {
  return (
    <div className="container mt-5">
      <div className="row justify-content-center">
        <div className="col-md-8">
          <div className="card">
            <div className="card-header bg-warning text-white">Compte en attente d'approbation</div>
            <div className="card-body text-center">
              <i className="fas fa-clock fa-4x text-warning mb-4"></i>
              <h2>Votre demande est en cours de traitement</h2>
              <p className="lead mt-3">
                Votre compte prestataire est actuellement en attente de validation par notre équipe.
                Vous recevrez un email dès que votre compte sera activé.
              </p>
              <Link to="/" className="btn btn-primary mt-3">
                Retour à l'accueil
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PendingApproval;