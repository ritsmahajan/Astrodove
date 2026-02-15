// netlify/functions/auth.js
// Authentication function for Astrodove admin

// Hardcoded credentials (will work immediately without env variables)
// You can change these later via environment variables
const ADMIN_USERNAME = process.env.ADMIN_USERNAME || 'rits_astrodove';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || 'lB*Wkq#4rFJFGJrN';

exports.handler = async (event, context) => {
  // Enable CORS
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type',
    'Access-Control-Allow-Methods': 'POST, OPTIONS',
    'Content-Type': 'application/json'
  };

  // Handle OPTIONS request (CORS preflight)
  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  // Only allow POST
  if (event.httpMethod !== 'POST') {
    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ error: 'Method not allowed' })
    };
  }

  try {
    const { username, password } = JSON.parse(event.body);

    // Verify credentials
    if (username === ADMIN_USERNAME && password === ADMIN_PASSWORD) {
      // Create session token
      const token = Buffer.from(`${username}:${Date.now()}`).toString('base64');
      
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          token: token,
          message: 'Login successful'
        })
      };
    } else {
      return {
        statusCode: 401,
        headers,
        body: JSON.stringify({
          success: false,
          error: 'Invalid credentials'
        })
      };
    }
  } catch (error) {
    console.error('Auth error:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        success: false,
        error: 'Server error: ' + error.message
      })
    };
  }
};
