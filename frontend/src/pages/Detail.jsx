import React from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import './Detail.css';

const Detail = () => {
  const { id } = useParams();
  const navigate = useNavigate();

  // Recettes mockées de base
  const mockRecettes = [
    {
      id: 1,
      nom: 'Salade Nicoise',
      categorie: 'ENTREES',
      ingredients: '200g de thon, 4 oeufs durs, olives noires',
      recette: 'Cuire les oeufs durs 10 min. Égoutter le thon. Disposer tous les ingrédients sur un lit de salade. Assaisonner avec de l\'huile d\'olive et du citron.'
    },
    {
      id: 2,
      nom: 'Crème Bruleé',
      categorie: 'DESSERTS',
      ingredients: "500ml crème entière, 6 jaunes d'oeufs, 120g sucre...",
      recette: "Préchauffer le four à 150°C. Mélanger les jaunes d'oeufs et le sucre. Ajouter la crème chaude. Verser dans des ramequins et cuire 40 min au bain-marie. Réfrigérer 2h puis caraméliser le sucre au chalumeau."
    },
    {
      id: 3,
      nom: 'Boeuf Bourguignon',
      categorie: 'PLATS',
      ingredients: '1kg boeuf à braiser, 750ml vin rouge, 200g lardons',
      recette: 'Faire revenir les lardons et le boeuf en morceaux. Ajouter les légumes, le vin rouge et le bouillon. Laisser mijoter 2h30 à feu doux jusqu\'à ce que la viande soit tendre.'
    },
    {
      id: 4,
      nom: "soupe a l'Oignon",
      categorie: 'ENTREES',
      ingredients: '1kg oignons, 1L bouillon de boeuf, 200ml vin blanc',
      recette: "Faire caraméliser les oignons émincés 30 min à feu doux. Déglacer au vin blanc. Ajouter le bouillon et laisser mijoter 20 min. Servir avec des croûtons et du gruyère gratinés."
    }
  ];

  // Chercher dans les mockées puis dans le localStorage
  const saved = JSON.parse(localStorage.getItem('recettes') || '[]');
  const toutes = [...mockRecettes, ...saved];
  const recette = toutes.find(r => String(r.id) === String(id));

  const getCategoryLabel = (categorie) => {
    const labels = {
      'ENTREES': 'Entrée',
      'DESSERTS': 'Dessert',
      'PLATS': 'Plat'
    };
    return labels[categorie] || categorie;
  };

  if (!recette) {
    return (
      <div className="detail-page">
        <div className="detail-not-found">
          <p>Recette introuvable.</p>
          <button className="detail-back-btn" onClick={() => navigate('/')}>
            ← Retour
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="detail-page">
      <div className="detail-container">

        <button className="detail-back-btn" onClick={() => navigate('/')}>
          ← Retour
        </button>

        <div className="detail-card">
          <span className={`badge badge-${recette.categorie.toLowerCase()}`}>
            {getCategoryLabel(recette.categorie)}
          </span>

          <h1 className="detail-nom">{recette.nom}</h1>

          <div className="detail-section">
            <h2 className="detail-section-title">🥕 Ingrédients</h2>
            <p className="detail-section-content">{recette.ingredients}</p>
          </div>

          <div className="detail-section">
            <h2 className="detail-section-title">👨‍🍳 Préparation</h2>
            <p className="detail-section-content">
              {recette.recette || 'Aucune préparation renseignée.'}
            </p>
          </div>
        </div>

      </div>
    </div>
  );
};

export default Detail;
