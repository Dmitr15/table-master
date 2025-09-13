import { BottomNavigation, BottomNavigationAction, Paper } from '@mui/material';
import { Link, useLocation } from 'react-router-dom';
import ConvertIcon from '@mui/icons-material/Transform';
import MergeIcon from '@mui/icons-material/Merge';
import SplitIcon from '@mui/icons-material/CallSplit';
import AnalyzeIcon from '@mui/icons-material/Analytics';
import HomeIcon from '@mui/icons-material/Home';

const Navigation = () => {
  const location = useLocation();

  return (
    <Paper sx={{ position: 'fixed', bottom: 0, left: 0, right: 0, zIndex: 1000 }} elevation={3}>
      <BottomNavigation
        showLabels
        value={location.pathname}
        sx={{ backgroundColor: 'background.paper' }}
      >
        <BottomNavigationAction
          label="Главная"
          value="/"
          icon={<HomeIcon />}
          component={Link}
          to="/"
        />
        <BottomNavigationAction
          label="Конвертер"
          value="/converter"
          icon={<ConvertIcon />}
          component={Link}
          to="/converter"
        />
        <BottomNavigationAction
          label="Слияние"
          value="/merger"
          icon={<MergeIcon />}
          component={Link}
          to="/merger"
        />
        <BottomNavigationAction
          label="Разделение"
          value="/splitter"
          icon={<SplitIcon />}
          component={Link}
          to="/splitter"
        />
        <BottomNavigationAction
          label="Анализ"
          value="/analyzer"
          icon={<AnalyzeIcon />}
          component={Link}
          to="/analyzer"
        />
      </BottomNavigation>
    </Paper>
  );
};

export default Navigation;