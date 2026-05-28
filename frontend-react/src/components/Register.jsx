import React, { useState } from 'react';
import api from '../api';
import { Link, useNavigate } from 'react-router-dom';

const Register = () => {
    const navigate = useNavigate();
    // 1. Ruajtja e të dhënave të formës në një objekt të vetëm shteti
    const [formData, setFormData] = useState({
        name: '',
        surname: '',
        email: '',
        birthday: '',
        password: '',
        confirm: '',
        agree: false
    });

    // 2. Ruajtja e erroreve për çdo fushë vizualisht
    const [errors, setErrors] = useState({});
    const [serverMessage, setServerMessage] = useState({ type: '', text: '' });
    const [loading, setLoading] = useState(false);

    // Funksioni që kap çdo shtypje tasti dhe përditëson shtetin automatikisht
    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value
        });
    };

    // FUNKSIONI I VALIDIMIT (Kopjuar plotësisht nga logjika jote në jQuery)
    const validateForm = () => {
        let tempErrors = {};
        let isValid = true;

        const letterRegex = /^[A-Za-z]+$/;
        const emailRegex = /\S+@\S+\.\S+/;
        // Përdorim rregullin tënd origjinal ose të Laravel-it të ri
        const passRegex = /^[a-zA-Z0-9!@#\$%\^\&*_=+-]{8,12}$/;

        if (!emailRegex.test(formData.email)) {
            tempErrors.email = "Email not valid";
            isValid = false;
        }

        if (!letterRegex.test(formData.name) || formData.name.length < 3) {
            tempErrors.name = "Name not valid (min 3 letters)";
            isValid = false;
        }

        if (!letterRegex.test(formData.surname) || formData.surname.length < 3) {
            tempErrors.surname = "Surname not valid";
            isValid = false;
        }

        if (!passRegex.test(formData.password)) {
            tempErrors.password = "Password not valid (8-12 chars)";
            isValid = false;
        }

        if (formData.confirm !== formData.password || formData.confirm === "") {
            tempErrors.confirm = "Passwords do not match";
            isValid = false;
        }

        if (formData.birthday !== '') {
            const birthDate = new Date(formData.birthday);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            if (age < 18) {
                tempErrors.birthday = "You must be older than 18";
                isValid = false;
            }
        } else {
            tempErrors.birthday = "Please select a date";
            isValid = false;
        }

        if (!formData.agree) {
            tempErrors.agree = "You must agree to the terms and policy";
            isValid = false;
        }

        setErrors(tempErrors);
        return isValid;
    };

    // NISJA E TË DHËNAVE DREJT LARAVEL API
    const handleSubmit = async (e) => {
        e.preventDefault();
        setServerMessage({ type: '', text: '' });
        setErrors({});

        if (!validateForm()) {
            return;
        }

        setLoading(true);
        console.log('Submitting register form', formData);

        try {
            const response = await api.post('/register', {
                name: formData.name,
                last_name: formData.surname,
                email: formData.email,
                birth_date: formData.birthday,
                password: formData.password,
                password_confirmation: formData.confirm
            });

            setServerMessage({ type: 'success', text: 'Regjistrimi u krye me sukses! Ju lutem verifikoni email-in.' });
            localStorage.setItem('verify_email', formData.email);
            setFormData({ name: '', surname: '', email: '', birthday: '', password: '', confirm: '', agree: false });
            navigate('/verify', { state: { email: formData.email } });
        } catch (error) {
            console.error('Register error', error);

            if (error.response && error.response.data.errors) {
                setErrors(error.response.data.errors);
            } else if (error.response && error.response.data.message) {
                setServerMessage({ type: 'error', text: error.response.data.message });
            } else if (error.message) {
                setServerMessage({ type: 'error', text: `Network error: ${error.message}` });
            } else {
                setServerMessage({ type: 'error', text: 'Ndodhi një gabim në server. Provoni përsëri.' });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="gray-bg" style={{ minHeight: '10vh', padding: '10px 0' }}>
            <div className="middle-box text-center loginscreen animated fadeInDown">
                <div>
                    <div>
                        <h2 className="logo-name">Shoe</h2>
                    </div>
                    <small>Register to our shoe store</small>
                    <p>Create account to see it in action.</p>

                    {serverMessage.text && (
                        <div className={`alert ${serverMessage.type === 'success' ? 'alert-success' : 'alert-danger'}`}>
                            {serverMessage.text}
                        </div>
                    )}

                    <form className="m-t" role="form" onSubmit={handleSubmit}>
                        
                        {/* INPUT NAME */}
                        <div className="form-group">
                            <input 
                                type="text" 
                                className="form-control" 
                                placeholder="Name" 
                                name="name"
                                value={formData.name}
                                onChange={handleChange}
                            />
                            {errors.name && <div className="text-danger text-left small">{errors.name}</div>}
                        </div>

                        {/* INPUT SURNAME */}
                        <div className="form-group">
                            <input 
                                type="text" 
                                className="form-control" 
                                placeholder="Surname" 
                                name="surname"
                                value={formData.surname}
                                onChange={handleChange}
                            />
                            {errors.surname && <div className="text-danger text-left small">{errors.surname}</div>}
                        </div>

                        {/* INPUT EMAIL */}
                        <div className="form-group">
                            <input 
                                type="email" 
                                className="form-control" 
                                placeholder="Email" 
                                name="email"
                                value={formData.email}
                                onChange={handleChange}
                            />
                            {errors.email && <div className="text-danger text-left small">{errors.email}</div>}
                        </div>

                        {/* INPUT BIRTHDAY */}
                        <div className="form-group">
                            <input 
                                type="date" 
                                className="form-control" 
                                name="birthday"
                                value={formData.birthday}
                                onChange={handleChange}
                            />
                            {errors.birthday && <div className="text-danger text-left small">{errors.birthday}</div>}
                        </div>

                        {/* INPUT PASSWORD */}
                        <div className="form-group">
                            <input 
                                type="password" 
                                className="form-control" 
                                placeholder="Password" 
                                name="password"
                                value={formData.password}
                                onChange={handleChange}
                            />
                            {errors.password && <div className="text-danger text-left small">{errors.password}</div>}
                        </div>

                        {/* INPUT CONFIRM PASSWORD */}
                        <div className="form-group">
                            <input 
                                type="password" 
                                className="form-control" 
                                placeholder="Confirm Password" 
                                name="confirm"
                                value={formData.confirm}
                                onChange={handleChange}
                            />
                            {errors.confirm && <div className="text-danger text-left small">{errors.confirm}</div>}
                        </div>

                        {/* CHECKBOX TERMS */}
                        <div className="form-group text-left">
                            <div className="checkbox i-checks">
                                <label> 
                                    <input 
                                        type="checkbox" 
                                        name="agree"
                                        checked={formData.agree}
                                        onChange={handleChange}
                                    /> 
                                    <i></i> Agree the terms and policy 
                                </label>
                            </div>
                            {errors.agree && <div className="text-danger small">{errors.agree}</div>}
                        </div>

                        <button type="submit" className="btn btn-primary block full-width m-b" disabled={loading}>
                            {loading ? 'Registering...' : 'Register'}
                        </button>

                        <p className="text-muted text-center"><small>Already have an account?</small></p>
                        <a className="btn btn-sm btn-white btn-block"><Link className="btn btn-sm btn-white btn-block" to="/login">Login</Link></a>
                    </form>
                    
                    <p className="m-t"> 
                       
                    </p>
                </div>
            </div>
        </div>
    );
};

export default Register;