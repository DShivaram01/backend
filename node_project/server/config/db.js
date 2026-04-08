const mongoose=require('mongoose');

const connectDB=async ()=>{
    try {
        const con=await mongoose.connect(process.env.MONGO_URI);
        console.log(`MongoDB Connected: ${con.connection.host}`);
    } catch (err) {
        console.error(`MongoDB Connection to MongoDB:, ${err.message}`);
        process.exit(1);
    }
};

module.exports=connectDB;