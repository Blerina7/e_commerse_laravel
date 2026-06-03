import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom'; // Importojmë Link dhe useNavigate për lëvizjen e faqeve
import api from '../api'; // Ura qendrore e Axios me Laravel-in

const Login = () => {
    const navigate = useNavigate(); // Vegël për ta çuar user-in te dashboard pasi bën login

    // 1. Ruajtja e të dhënave të formës në shtet
    const [formData, setFormData] = useState({
        email: '',
        password: ''
    });

    // 2. Ruajtja e erroreve për validim lokalisht (fiks si jQuery yt)
    const [errors, setErrors] = useState({});
    const [errorMessage, setErrorMessage] = useState('');
    const [loading, setLoading] = useState(false);

    // Kjo kap shkronjat në inpute në kohë reale
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData({
            ...formData,
            [name]: value
        });
    };

    // FUNKSIONI I VALIDIMIT (Kopjuar ekzaktësisht nga funksioni yt 'valido()')
    const validateForm = () => {
        let tempErrors = {};
        let isValid = true;

        const emailRegex = /\S+@\S+\.\S+/;
        // Përdorim regex-in tënd origjinal të fjalëkalimit
        const passRegex = /^[a-zA-Z0-9!@#\$%\^\&*_=+-]{8,12}$/;

        if (!emailRegex.test(formData.email)) {
            tempErrors.email = "Email not valid";
            isValid = false;
        }

        if (!passRegex.test(formData.password)) {
            tempErrors.password = "Password not valid";
            isValid = false;
        }

        setErrors(tempErrors);
        return isValid;
    };

    // DËRGIMI I KËRKESËS TE BACKEND-I
    const handleSubmit = async (e) => {
        e.preventDefault(); // Ndalojmë formën të dërgohet në mënyrë klasike HTML
        setErrorMessage('');
        setErrors({});

        // Ekzekutojmë kushtin e vjetër të jQuery-t tënd
        if (validateForm()) {
            setLoading(true);
            try {
                // Thërrasim rrugën tënde në Laravel
                const response = await api.post('/login', {
                    email: formData.email,
                    password: formData.password
                });

                // Ruajmë Token-in e sigurisë në browser
                const token = response.data.token;
                localStorage.setItem('token_mini_amazon', token);

                // Ruajmë rolin që të kontrollojmë nëse është admin/manager/customer
                localStorage.setItem('user_role', response.data.user.role);

                alert("Login u krye me sukses!");
                
                // Pasi futet me sukses, Router-i e çon automatikisht te tabela e adminit
                navigate('/'); 

            } catch (error) {
                // Kapim gabimet nëse kredencialet nuk përputhen në databazë
                if (error.response && error.response.status === 401) {
                    setErrorMessage("Email ose fjalëkalimi është i pasaktë.");
                } else if (error.response && error.response.data.errors) {
                    setErrors(error.response.data.errors);
                } else {
                    setErrorMessage("Ndodhi një gabim në server. Provoni përsëri.");
                }
            } finally {
                setLoading(false);
            }
        }
    };

    return (
        <div className="gray-bg" style={{ minHeight: '100vh', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <div className="middle-box text-center loginscreen animated fadeInDown" style={{ marginTop: '0' }}>
                <div>
                    <div>
                        <h1 className="logo-name">IN+</h1>
                    </div>
                    <h3>Welcome to SHOE STORE</h3>
                    <p>Login in. To see it in action.</p>

                    {/* Alerter në rast se të dhënat janë gabim në server */}
                    {errorMessage && (
                        <div className="alert alert-danger small">
                            {errorMessage}
                        </div>
                    )}

                    <form className="m-t" role="form" onSubmit={handleSubmit}>
                        
                        {/* INPUT EMAIL */}
                        <div className="form-group">
                            <input 
                                type="email" 
                                className="form-control" 
                                placeholder="Email" 
                                name="email"
                                id="email"
                                value={formData.email}
                                onChange={handleChange}
                            />
                            {errors.email && <div id="email_error" className="text-danger text-left small">{errors.email}</div>}
                        </div>

                        {/* INPUT PASSWORD */}
                        <div className="form-group">
                            <input 
                                type="password" 
                                className="form-control" 
                                placeholder="Password" 
                                name="password"
                                id="password"
                                value={formData.password}
                                onChange={handleChange}
                            />
                            {errors.password && <div id="password_error" className="text-danger text-left small">{errors.password}</div>}
                        </div>

                        {/* BUTTON SUBMIT */}
                        <button 
                            type="submit" 
                            className="btn btn-primary block full-width m-b"
                            disabled={loading}
                        >
                            {loading ? 'Duke u lidhur...' : 'Login'}
                        </button>

                        <p className="text-muted text-center"><small>Forgot your password?</small></p>
                        
                        {/* LIDHJA E RE ME REACT ROUTER NË VEND TË RESET.PHP */}
                        <Link className="btn btn-sm btn-white btn-block" to="/reset-password">
                            Reset your password
                        </Link>
                        <br />
                        
                        <p className="text-muted text-center"><small>Do not have an account?</small></p>
                        
                        {/* LIDHJA E RE ME REACT ROUTER NË VEND TË REGISTER.PHP */}
                        <Link className="btn btn-sm btn-white btn-block" to="/register">
                            Create an account
                        </Link>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Login;