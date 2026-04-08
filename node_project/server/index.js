require('dotenv').config();

const express = require('express');
const productRoutes = require('./routes/product.route.js');
const connectDB = require('./config/db.js');

const app = express();
const PORT = process.env.PORT || 3000;


// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Connect to MongoDB
connectDB();

// Use Routes
app.use('/api/products', productRoutes);

//Start the server
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
});


