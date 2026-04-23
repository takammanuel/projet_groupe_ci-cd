import React from 'react';
import './Sidebar.css';

const Sidebar = ({ activeCategory, onCategoryChange }) => {
  const categories = ['Toutes', 'Entrées', 'Desserts', 'Plats'];

  return (
    <aside className="sidebar">
      <h3 className="sidebar-title">Catégories</h3>
      <ul className="category-list">
        {categories.map((category) => (
          <li
            key={category}
            className={`category-item ${activeCategory === category ? 'active' : ''}`}
            onClick={() => onCategoryChange(category)}
          >
            {category}
          </li>
        ))}
      </ul>
    </aside>
  );
};

export default Sidebar;
