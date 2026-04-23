import React, { useState, useEffect } from 'react';
import RecetteCard from './RecetteCard';
import './RecetteList.css';

const RecetteList = ({ activeCategory }) => {
  const [recettes, setRecettes] = useState([]);
  const [filteredRecettes, setFilteredRecettes] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');

  // Données mockées
  const mockRecettes = [
    {
      id: 1,
      nom: 'Salade Nicoise',
      categorie: 'ENTREES',
      ingredients: '200g de thon, 4 oeufs durs, olives noires'
    },
    {
      id: 2,
      nom: 'Crème Bruleé',
      categorie: 'DESSERTS',
      ingredients: '500ml crème entière, 6 jaunes d\'oeufs, 120g sucre...'
    },
    {
      id: 3,
      nom: 'Boeuf Bourguignon',
      categorie: 'PLATS',
      ingredients: '1kg boeuf à braiser, 750ml vin rouge, 200g lardons'
    },
    {
      id: 4,
      nom: 'soupe a l\'Oignon',
      categorie: 'ENTREES',
      ingredients: '1kg oignons, 1L bouillon de boeuf, 200m vin blanc'
    }
  ];

  useEffect(() => {
    setRecettes(mockRecettes);
    setFilteredRecettes(mockRecettes);
  }, []);

  useEffect(() => {
    let filtered = recettes;

    // Filtrer par catégorie
    if (activeCategory !== 'Toutes') {
      filtered = filtered.filter(r => {
        const categoryMap = {
          'Entrées': 'ENTREES',
          'Desserts': 'DESSERTS',
          'Plats': 'PLATS'
        };
        return r.categorie === categoryMap[activeCategory];
      });
    }

    // Filtrer par recherche
    if (searchQuery) {
      filtered = filtered.filter(r =>
        r.nom.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    setFilteredRecettes(filtered);
  }, [activeCategory, searchQuery, recettes]);

  return (
    <div className="recettes-container">
      <div className="recettes-header">
        <h2 className="recettes-title">Toutes les recettes</h2>
        <input
          type="search"
          className="search-input"
          placeholder="Rechercher une recette..."
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
      </div>
      
      <div className="recettes-grid">
        {filteredRecettes.map((recette) => (
          <RecetteCard key={recette.id} recette={recette} />
        ))}
      </div>
      
      {filteredRecettes.length === 0 && (
        <div className="no-results">
          Aucune recette trouvée
        </div>
      )}
    </div>
  );
};

export default RecetteList;
