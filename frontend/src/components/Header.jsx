import React from 'react';
import './Header.css';

const Header = () => {
  return (
    <header className="header">
      <h1 className="logo">La Table</h1>
      <button className="btn-add">+ Ajouter une recette</button>
    </header>
  );
};

export default Header;
