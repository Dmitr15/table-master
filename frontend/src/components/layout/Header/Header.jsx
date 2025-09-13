import { AppBar, Toolbar, Typography, Switch, Box } from '@mui/material';
import LightModeIcon from '@mui/icons-material/LightMode';
import DarkModeIcon from '@mui/icons-material/DarkMode';

const Header = ({ darkMode, onToggleDarkMode }) => {
  return (
    <AppBar position="static">
      <Toolbar>
        <Typography variant="h6" component="div" sx={{ flexGrow: 1 }}>
          Table Master
        </Typography>
        <Box display="flex" alignItems="center">
          <LightModeIcon fontSize="small" />
          <Switch
            checked={darkMode}
            onChange={onToggleDarkMode}
            color="default"
          />
          <DarkModeIcon fontSize="small" />
        </Box>
      </Toolbar>
    </AppBar>
  );
};

export default Header;