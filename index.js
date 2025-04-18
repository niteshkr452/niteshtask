const express = require("express");
const mongoose = require("mongoose");
const cors = require("cors");

const app = express();

// Middleware
app.use(express.json());
app.use(cors());

// MongoDB Connection
mongoose.connect("mongodb+srv://niteshraj35yd:95238828sn@cluster0.x721d.mongodb.net/primetask", {
    useNewUrlParser: true,
    useUnifiedTopology: true
})
    .then(() => console.log("✅ MongoDB Connected"))
    .catch((err) => console.error("❌ MongoDB Connection Error:", err));

// Define Schema with required fields
const userSchema = new mongoose.Schema({
    name: String,
    email: String,
    subject: String,
    message: String
});

// Create Model
const UserModel = mongoose.model("User", userSchema);

// Function to Save a User
const saveUser = async () => {
    try {
        const emp1 = new UserModel({
            name: "Nitesh",
            email: "niteshraj355yadav@gmail.com",
            subject: "Complete",
            message: "Hi"
        });

        await emp1.save();
        console.log("✅ Data saved successfully!");
    } catch (err) {
        console.error("❌ Error saving data:", err);
    }
};

// Call saveUser function only after connection is established
mongoose.connection.once("open", () => {
    saveUser();
});

// Start Server
const PORT = process.env.PORT || 5500;
app.listen(PORT, () => {
    console.log(`🚀 Server is running on http://localhost:${PORT}`);
});
