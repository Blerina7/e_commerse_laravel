import React, { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import api from '../api'; // Ura qendrore e Axios

const Verify = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const initialEmail = location.state?.email || localStorage.getItem('verify_email') || '';
    const [email, setEmail] = useState(initialEmail);
    
    // Shteti për të ruajtur kodin që shkruan user-i
    const [code, setCode] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError('');

        if (!email) {
            setError('Please enter your email address.');
            return;
        }

        setLoading(true);

        try {
            const response = await api.post('/verify-email', {
                email: email,
                code: code
            });

            alert("Llogaria juaj u verifikua me sukses! Tani mund të hyni.");
            navigate('/login');

        } catch (err) {
            if (err.response && err.response.data.message) {
                setError(err.response.data.message);
            } else {
                setError("Kodi i vendosur nuk është i saktë. Provoni përsëri.");
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="gray-bg" style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <div className="middle-box text-center loginscreen animated fadeInDown" style={{ marginTop: '0' }}>
                <div>
                    <div>
                        <h1 className="logo-name">IN+</h1>
                    </div>
                    <h3>Verify Your Account</h3>
                    <p>Please enter the verification code sent to your email.</p>

                    {/* Shfaqja e errorit nëse kodi është i pasaktë */}
                    {error && (
                        <div className="alert alert-danger small">
                            {error}
                        </div>
                    )}

                    <form className="m-t" role="form" onSubmit={handleSubmit}>
                        <div className="form-group">
                            <input 
                                type="email" 
                                className="form-control" 
                                placeholder="Email" 
                                required
                                id="email" 
                                name="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                            />
                        </div>
                        <div className="form-group">
                            <input 
                                type="number" 
                                className="form-control" 
                                placeholder="Your code" 
                                required
                                id="code" 
                                name="code"
                                value={code}
                                onChange={(e) => setCode(e.target.value)}
                            />
                        </div>
                        
                        <button 
                            type="submit" 
                            className="btn btn-primary block full-width m-b" 
                            disabled={loading}
                        >
                            {loading ? 'Duke u verifikuar...' : 'Verify'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Verify;