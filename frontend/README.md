# Cybersecurity Classes Frontend

A modern Vue 3 frontend for the Cybersecurity Class Booking Platform, built with Vite, TailwindCSS, and Pinia.

## Features

- 🎨 **Modern UI**: Built with TailwindCSS and Heroicons
- 🔐 **Secure Authentication**: JWT tokens with refresh mechanism
- 📱 **Responsive Design**: Mobile-first approach
- 🛡️ **Security**: reCAPTCHA v3, CSRF protection, input validation
- 💳 **Payment Integration**: Paystack payment processing
- 🤖 **AI Assistant**: Built-in chat widget for user support
- 📊 **Admin Dashboard**: Package management, booking overview, broadcast messaging

## Tech Stack

- **Framework**: Vue 3 with Composition API
- **Build Tool**: Vite
- **Styling**: TailwindCSS
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **HTTP Client**: Axios
- **Icons**: Heroicons
- **Notifications**: Vue Toastification
- **Date Handling**: date-fns

## Prerequisites

- Node.js 16+ 
- npm or yarn
- Backend API running (see backend README)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Environment Configuration**
   Create a `.env` file in the frontend directory:
   ```env
   VITE_API_URL=http://localhost:8080
   VITE_RECAPTCHA_SITE_KEY=your_recaptcha_site_key
   VITE_TAWK_TO_WIDGET_ID=your_tawk_to_widget_id
   ```

4. **Update Configuration**
   - Replace `YOUR_RECAPTCHA_SITE_KEY` in `index.html` with your actual reCAPTCHA site key
   - Replace `YOUR_TAWK_TO_WIDGET_ID` in `index.html` with your Tawk.to widget ID

## Development

```bash
# Start development server
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Lint code
npm run lint
```

The development server will start at `http://localhost:5173`

## Project Structure

```
frontend/
├── public/                 # Static assets
├── src/
│   ├── assets/            # Images, logos
│   ├── components/        # Reusable UI components
│   │   └── AIChat.vue     # AI assistant chat widget
│   ├── router/            # Vue Router configuration
│   │   └── index.js       # Route definitions and guards
│   ├── store/             # Pinia stores
│   │   └── auth.js        # Authentication state management
│   ├── views/             # Page components
│   │   ├── Home.vue       # Landing page
│   │   ├── Login.vue      # User login
│   │   ├── Register.vue   # User registration
│   │   ├── Packages.vue   # Package listing
│   │   ├── Booking.vue    # Booking form
│   │   ├── Dashboard.vue  # User dashboard
│   │   ├── Admin.vue      # Admin dashboard
│   │   └── NotFound.vue   # 404 page
│   ├── services/          # API services
│   │   └── api.js         # Axios configuration
│   ├── App.vue            # Root component
│   ├── main.js            # Application entry point
│   └── style.css          # Global styles
├── package.json           # Dependencies and scripts
├── vite.config.js         # Vite configuration
├── tailwind.config.js     # TailwindCSS configuration
└── postcss.config.js      # PostCSS configuration
```

## Key Features

### Authentication
- Secure login/registration with reCAPTCHA v3
- JWT access tokens with automatic refresh
- Password policy validation
- Protected routes with navigation guards

### Booking System
- Package selection and date picking
- Paystack payment integration
- Booking confirmation and status tracking
- WhatsApp and Discord community links

### Admin Features
- Package CRUD operations
- Booking management
- User statistics
- Broadcast messaging to all users

### Security
- CSRF token protection
- Input validation and sanitization
- Secure token storage
- CORS configuration

## API Integration

The frontend communicates with the backend API through the `api.js` service:

- **Base URL**: Configured via `VITE_API_URL` environment variable
- **Authentication**: JWT tokens in Authorization header
- **CSRF Protection**: Automatic token fetching for non-GET requests
- **Error Handling**: Automatic token refresh on 401 errors

## Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `VITE_API_URL` | Backend API URL | Yes |
| `VITE_RECAPTCHA_SITE_KEY` | Google reCAPTCHA site key | Yes |
| `VITE_TAWK_TO_WIDGET_ID` | Tawk.to chat widget ID | No |

## Deployment

1. **Build the application**
   ```bash
   npm run build
   ```

2. **Deploy the `dist` folder** to your web server

3. **Configure environment variables** on your production server

4. **Set up HTTPS** for security (required for reCAPTCHA and secure cookies)

## Browser Support

- Chrome 88+
- Firefox 85+
- Safari 14+
- Edge 88+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.