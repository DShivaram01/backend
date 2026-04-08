const Product=require('../models/product.model.js');

const createProduct=async (req,res)=>{
    try {
            const product = await Product.create(req.body);
            res.status(201).json(product);
        } catch (error) {
            res.status(500).json({ message: error.message });
        }


};
const getProducts=async (req,res)=>{
    try {
        const products = await Product.find({});
        res.status(200).json(products);
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};
const getProductById=async (req,res)=>{
    try {
        const product=await Product.findById(req.params.id);
        if (!product) {
            return res.status(404).json({ message: 'Product not found' });
        }
        res.status(200).json(product);
    } catch (error) {
        console.error("Error handling GET request:",error.error); 
        res.status(500).json({error: 'Internal Server Error' });
    }


};
const deleteProduct=async (req,res)=>{
    try {
        const product = await Product.findByIdAndDelete(req.params.id);
        if (!product) {
            return res.status(404).json({ message: 'Product not found' });
        }
        res.status(200).json({ message: 'Product deleted successfully' });
    } catch (error) {
        console.error("Error handling DELETE request:", error);
        res.status(500).json({ error: 'Internal Server Error' });
        }
};
const updateProduct=async (req,res)=>{
    try {
        const product = await Product.findByIdAndUpdate(req.params.id, req.body);
        if (!product) {
            return res.status(404).json({ message: 'Product not found' });
        }
        const updatedProduct = await Product.findById(req.params.id);
        res.status(200).json(updatedProduct);
    } catch (error) {
        console.error("Error handling PUT request:", error.message);
        res.status(500).json({ error: 'Internal Server Error' });
    }
};

const searchProducts = async (req, res) => {
    try {
        if (!req.query.name) return res.json([]);

        const products = await Product.find({
            name: { $regex: req.query.name, $options: 'i' }
        });

        res.json(products);

    } catch (error) {
        res.status(500).json({ message: error.message });
    }
};

module.exports={
    createProduct,
    getProducts,
    getProductById,
    deleteProduct,
    updateProduct,
    searchProducts
}