import React, { useState, useEffect } from 'react';
import Header from '../components/Header';
import Sidebar from '../components/Sidebar';
import RecetteList from '../components/RecetteList';
import './Home.css';

const Home = () => {
  const [activeCategory, setActiveCategory] = useState('Toutes');

  return (
    <div className="home-container">
      <Header />
      <div className="home-content">
        <Sidebar 
          activeCategory={activeCategory}
          onCategoryChange={setActiveCategory}
        />
        <main className="main-content">
          <RecetteList activeCategory={activeCategory} />
        </main>
      </div>
    </div>
  );
};

export default Home;
