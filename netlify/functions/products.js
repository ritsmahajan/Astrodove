// netlify/functions/products.js
// Product management - simplified version without Netlify Blobs
// Products will be stored in a simple JSON approach

// Temporary storage (in production, this would be a database)
// For now, we'll use Netlify's built-in file system

exports.handler = async (event, context) => {
  const headers = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization',
    'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
    'Content-Type': 'application/json'
  };

  if (event.httpMethod === 'OPTIONS') {
    return { statusCode: 200, headers, body: '' };
  }

  try {
    const method = event.httpMethod;

    // For now, return empty products array
    // This allows the site to work without database
    // Products will be stored in browser localStorage as fallback
    
    if (method === 'GET') {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({ 
          success: true, 
          products: [],
          message: 'Using localStorage fallback - add products via admin panel'
        })
      };
    }

    if (method === 'POST') {
      // Accept product but don't store (localStorage will handle it)
      const product = JSON.parse(event.body);
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          message: 'Product saved to localStorage',
          product: product
        })
      };
    }

    if (method === 'DELETE') {
      return {
        statusCode: 200,
        headers,
        body: JSON.stringify({
          success: true,
          message: 'Product deleted from localStorage'
        })
      };
    }

    return {
      statusCode: 405,
      headers,
      body: JSON.stringify({ error: 'Method not allowed' })
    };

  } catch (error) {
    console.error('Error:', error);
    return {
      statusCode: 500,
      headers,
      body: JSON.stringify({
        success: false,
        error: error.message || 'Server error'
      })
    };
  }
};
