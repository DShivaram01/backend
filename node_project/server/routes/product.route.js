const express = require('express');
const router = express.Router();
const { createProduct, getProducts, getProductById, deleteProduct, updateProduct,searchProducts} = require('../controllers/product.controller.js');

router.post('/', createProduct);
router.get('/', getProducts);
router.get('/search', searchProducts);
router.get('/:id', getProductById);
router.delete('/:id', deleteProduct);
router.put('/:id', updateProduct);
router.get('/search', searchProducts);

module.exports = router;