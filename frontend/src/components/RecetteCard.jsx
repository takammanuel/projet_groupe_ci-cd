import React from 'react';
import './RecetteCard.css';

const RecetteCard = ({ recette }) => {
  const getCategoryLabel = (categorie) => {
    const labels = {
      'ENTREES': 'ENTRÉES',
      'DESSERTS': 'DESSERTS',
      'PLATS': 'PLATS'
    };
    return labels[categorie] || categorie;
  };

  return (
    <div className="recette-card">
      <span className={`badge badge-${recette.categorie.toLowerCase()}`}>
        {getCategoryLabel(recette.categorie)}
      </span>
      <h3 className="recette-nom">{recette.nom}</h3>
      <p className="recette-ingredients">
        <strong>Ingrédients :</strong><br />
        {recette.ingredients}
      </p>
      <button className="btn-detail">Voir le détail</button>
    </div>
  );
};

export default RecetteCard;
