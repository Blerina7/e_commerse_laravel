import React, { useState } from 'react';
import axios from 'axios';
import api from '../api';

const Register = () => {
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
        e.preventDefault(); // Ndalon refresh-in e faqes që bënte HTML e vjetër
        setServerMessage({ type: '', text: '' });
        const response = await api.post('/register', formData);

        if (validateForm()) {
            try {
                // Thërrasim endpoint-in tënd të regjistrimit në Laravel
                const response = await axios.post('http://127.0.0.1:8000/api/register', {
                    name: formData.name,
                    last_name: formData.surname, // Përshtatur me 'last_name' të Backend-it
                    email: formData.email,
                    birth_date: formData.birthday, // Përshtatur me 'birth_date' të Backend-it
                    password: formData.password,
                    password_confirmation: formData.confirm // Laravel kërkon këtë emër për konfirmim
                });

                setServerMessage({ type: 'success', text: 'Regjistrimi u krye me sukses! Ju lutem verifikoni email-in.' });
                
                // Pastrojmë formën pas suksesit
                setFormData({ name: '', surname: '', email: '', birthday: '', password: '', confirm: '', agree: false });

            } catch (error) {
                // Nëse Laravel kthen errore validimi (psh email ekziston), i kapim këtu
                if (error.response && error.response.data.errors) {
                    setErrors(error.response.data.errors);
                } else {
                    setServerMessage({ type: 'error', text: 'Ndodhi një gabim në server. Provoni përsëri.' });
                }
            }
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

                        <button type="submit" className="btn btn-primary block full-width m-b">Register</button>

                        <p className="text-muted text-center"><small>Already have an account?</small></p>
                        <a className="btn btn-sm btn-white btn-block" href="/login">Login</a>
                    </form>
                    
                    <p className="m-t"> 
                       
                    </p>
                </div>
            </div>
        </div>
    );
};

export default Register;