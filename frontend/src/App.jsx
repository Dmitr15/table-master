import { useState } from 'react';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import { CssBaseline, Box, Paper } from '@mui/material';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';

// Импорт компонентов
import Header from './components/layout/Header/Header';
import Navigation from './components/layout/Navigation/Navigation';
import Notification from './components/common/Notification/Notification';

// Импорт модулей
import Converter from './modules/Converter/Converter';
import Merger from './modules/Merger/Merger';
import Splitter from './modules/Splitter/Splitter';
import Analyzer from './modules/Analyzer/Analyzer';
import Home from './modules/Home/Home';

// Импорт контекстов
import { NotificationProvider } from './contexts/NotificationContext';

// Импорт стилей
import './App.css';

// Создание темы Material-UI
const lightTheme = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
  },
});

const darkTheme = createTheme({
  palette: {
    mode: 'dark',
    primary: {
      main: '#90caf9',
    },
    secondary: {
      main: '#f48fb1',
    },
  },
});

function App() {
  const [darkMode, setDarkMode] = useState(false);
  const currentTheme = darkMode ? darkTheme : lightTheme;

  const toggleDarkMode = () => {
    setDarkMode(!darkMode);
  };

  return (
    <ThemeProvider theme={currentTheme}>
      <CssBaseline /> {/* Нормализация стилей Material-UI */}
      <NotificationProvider>
        <Router>
          <Box className="app-container">
            {/* Шапка приложения */}
            <Header darkMode={darkMode} onToggleDarkMode={toggleDarkMode} />
            
            {/* Основная навигация */}
            <Navigation />
            
            {/* Главное содержимое */}
            <Box component="main" className="main-content">
              <Paper 
                elevation={2} 
                sx={{ 
                  p: 3, 
                  minHeight: '400px',
                  borderRadius: 2
                }}
              >
                <Routes>
                  <Route path="/" element={<Home />} />
                  <Route path="/converter" element={<Converter />} />
                  <Route path="/merger" element={<Merger />} />
                  <Route path="/splitter" element={<Splitter />} />
                  <Route path="/analyzer" element={<Analyzer />} />
                  
                  {/* Резервный route для 404 */}
                  <Route path="*" element={
                    <Box textAlign="center" py={8}>
                      <h2>Страница не найдена</h2>
                      <p>Запрошенная страница не существует.</p>
                    </Box>
                  } />
                </Routes>
              </Paper>
            </Box>
            
            {/* Уведомления */}
            <Notification />
          </Box>
        </Router>
      </NotificationProvider>
    </ThemeProvider>
  );
}

export default App;