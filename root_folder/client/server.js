const express = require('express');
const path = require('path');

const app = express();
const PORT = 3022;

app.use(express.static(path.join(__dirname,'public')));

// Serve the index.html file
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'html', 'index.html'));
});

// Log incoming requests to static files
app.use((req, res, next) => {
    console.log(`Requested: ${req.url}`);
    next();
});

app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});