import apiClient from './api';

export const authService = {
  async login(email, password) {
    const response = await apiClient.post('/login', {
      email,
      password
    });
    
    const { access_token, user } = response.data;
    
    localStorage.setItem('auth_token', access_token);
    localStorage.setItem('user', JSON.stringify(user));
    
    return { token: access_token, user };
  },

  async logout() {
    try {
      await apiClient.post('/logout');
    } finally {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
  },

  isAuthenticated() {
    return !!localStorage.getItem('auth_token');
  },

  getToken() {
    return localStorage.getItem('auth_token');
  },

  getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }
};
