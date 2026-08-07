import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

const Dashboard = () => {
	const [activeTab, setActiveTab] = useState('analytics');
	const [stats, setStats] = useState({ users: 0, articles: 0, total_views: 0 });
	const [users, setUsers] = useState([]);
	const [researchers, setResearchers] = useState([]);
	const [articles, setArticles] = useState([]);
	const [certificates, setCertificates] = useState([]);
	const [verificationRequests, setVerificationRequests] = useState([]);
	const [settings, setSettings] = useState({ site_name: '', site_desc: '', admin_email: '', mock_data_seeded: false, enable_registration: 'yes', auth_maintenance_mode: 'no', privacy_policy_url: '', terms_url: '' });
	const [loading, setLoading] = useState(true);
	const [sidebarOpen, setSidebarOpen] = useState(false);

	// Settings sub-tabs
	const [settingsTab, setSettingsTab] = useState('general');

	// Articles sub-tabs
	const [articlesTab, setArticlesTab] = useState('post');

	// Search state
	const [userSearch, setUserSearch] = useState('');
	const [researcherSearch, setResearcherSearch] = useState('');
	const [articleSearch, setArticleSearch] = useState('');
	const [articleStatusFilter, setArticleStatusFilter] = useState('all');
	const [articleCategoryFilter, setArticleCategoryFilter] = useState('all');
	const [articleSort, setArticleSort] = useState('desc');
	const [selectedArticles, setSelectedArticles] = useState([]);
	const [certificateSearch, setCertificateSearch] = useState('');

	// Modals
	const [showUserModal, setShowUserModal] = useState(false);
	const [editingUser, setEditingUser] = useState(null);
	const [isRestrictedChecked, setIsRestrictedChecked] = useState(false);
	const [showResearcherModal, setShowResearcherModal] = useState(false);
	const [editingResearcher, setEditingResearcher] = useState(null);
	const [showCertificateModal, setShowCertificateModal] = useState(false);
	const [editingCertificate, setEditingCertificate] = useState(null);

	const apiFetch = async (endpoint, options = {}) => {
		const nonce = window.healthediaDashboardSettings?.nonce || '';
		const headers = {
			'Content-Type': 'application/json',
			...options.headers
		};
		if (nonce) {
			headers['X-WP-Nonce'] = nonce;
		}

		const res = await fetch(`/wp-json/healthedia/v1/admin/${endpoint}`, {
			...options,
			headers: headers
		});

		if (res.status === 401 || res.status === 403) {
			if (!nonce) {
				console.error("API Error: Missing WP Nonce. Make sure healthediaDashboardSettings is properly injected by WordPress.");
			} else {
				window.location.href = '/login'; // Redirect unauthenticated/unauthorized users immediately
			}
			throw new Error('Unauthorized');
		}
		if (!res.ok) throw new Error('API Error');
		return res.json();
	};

	const loadData = async () => {
		setLoading(true);
		try {
			if (activeTab === 'analytics') setStats(await apiFetch('stats'));
			else if (activeTab === 'users') setUsers(await apiFetch('users'));
			else if (activeTab === 'researchers') setResearchers(await apiFetch('researchers'));
			else if (activeTab === 'articles') setArticles(await apiFetch(`articles?type=${articlesTab}`));
			else if (activeTab === 'certificates') setCertificates(await apiFetch('certificates'));
			else if (activeTab === 'verifications') setVerificationRequests(await apiFetch('verifications'));
			else if (activeTab === 'settings') setSettings(await apiFetch('settings'));
		} catch (err) {
			console.error('Failed to load data:', err);
		} finally {
			setLoading(false);
		}
	};

	const deleteArticle = async (id) => {
		if(window.confirm('Delete article?')) {
			try { await apiFetch(`articles/${id}`, { method: 'DELETE' }); loadData(); } catch(e) { alert('Error deleting article'); }
		}
	};

	// Certificates CRUD
	const handleCertificateSubmit = async (e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const data = Object.fromEntries(formData.entries());
		try {
			if (editingCertificate) {
				await apiFetch(`certificates/${editingCertificate.id}`, { method: 'PUT', body: JSON.stringify(data) });
			} else {
				await apiFetch('certificates', { method: 'POST', body: JSON.stringify(data) });
			}
			setShowCertificateModal(false);
			loadData();
		} catch(err) { alert('Error saving certificate.'); }
	};

	const deleteCertificate = async (id) => {
		if(window.confirm('Delete certificate?')) {
			try { await apiFetch(`certificates/${id}`, { method: 'DELETE' }); loadData(); } catch(e) { alert('Error deleting certificate'); }
		}
	};

	useEffect(() => {
		loadData();
	}, [activeTab, articlesTab]);

	const saveSettings = async (e) => {
		e.preventDefault();
		try {
			await apiFetch('settings', { method: 'POST', body: JSON.stringify(settings) });
			alert('Settings saved.');
		} catch (e) {
			alert('Error saving settings.');
		}
	};

	const wipeMockData = async () => {
		if (window.confirm("Are you sure you want to permanently delete all mock data?")) {
			try {
				await apiFetch('wipe-mock-data', { method: 'POST' });
				alert('Mock data wiped.');
				setSettings({ ...settings, mock_data_seeded: false });
			} catch (e) {
				alert('Error wiping mock data.');
			}
		}
	};

	// Users CRUD
	const handleUserSubmit = async (e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const data = Object.fromEntries(formData.entries());
		data.roles = formData.getAll('roles[]');
		try {
			if (editingUser) {
				await apiFetch(`users/${editingUser.id}`, { method: 'PUT', body: JSON.stringify(data) });
			} else {
				await apiFetch('users', { method: 'POST', body: JSON.stringify(data) });
			}
			setShowUserModal(false);
			loadData();
		} catch(err) { alert('Error saving user.'); }
	};

	const deleteUser = async (id) => {
		if(window.confirm('Delete user?')) {
			try { await apiFetch(`users/${id}`, { method: 'DELETE' }); loadData(); } catch(e) { alert('Error deleting user'); }
		}
	};

	// Researchers CRUD
	const handleResearcherSubmit = async (e) => {
		e.preventDefault();
		const formData = new FormData(e.target);
		const data = Object.fromEntries(formData.entries());
		try {
			if (editingResearcher) {
				await apiFetch(`researchers/${editingResearcher.id}`, { method: 'PUT', body: JSON.stringify(data) });
			} else {
				await apiFetch('researchers', { method: 'POST', body: JSON.stringify(data) });
			}
			setShowResearcherModal(false);
			loadData();
		} catch(err) { alert('Error saving researcher.'); }
	};

	const deleteResearcher = async (id) => {
		if(window.confirm('Delete researcher?')) {
			try { await apiFetch(`researchers/${id}`, { method: 'DELETE' }); loadData(); } catch(e) { alert('Error deleting researcher'); }
		}
	};

	const closeSidebar = () => setSidebarOpen(false);

	return (
		<div className="min-h-screen bg-gray-50 text-[#111111] font-sans flex flex-col md:flex-row">
			<div className="md:hidden flex items-center justify-between bg-white border-b border-[#E0E0E0] px-4 py-3 sticky top-0 z-30">
				<h1 className="text-xl font-bold uppercase tracking-tight">Healthedia<span className="text-[10px] font-mono text-gray-400 uppercase tracking-widest ml-2">System</span></h1>
				<button onClick={() => setSidebarOpen(!sidebarOpen)} className="p-2 border border-[#E0E0E0] rounded-lg">
					<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
				</button>
			</div>

			{sidebarOpen && <div className="fixed inset-0 bg-black/50 z-40 md:hidden" onClick={closeSidebar}></div>}

			<aside className={`fixed md:sticky top-0 left-0 h-screen md:h-screen w-64 bg-white border-r border-[#E0E0E0] p-6 flex-shrink-0 flex flex-col z-50 transform ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} md:translate-x-0 transition-transform duration-300 overflow-y-auto`}>
				<div className="flex justify-between items-center mb-12">
					<h1 className="text-2xl font-bold uppercase tracking-tight hidden md:block">Healthedia<br/><span className="text-[10px] font-mono text-gray-400 uppercase tracking-widest">Internal System</span></h1>
					<h1 className="text-xl font-bold uppercase tracking-tight md:hidden">Menu</h1>
					<button onClick={closeSidebar} className="md:hidden p-2 text-gray-500">
						<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
					</button>
				</div>

				<nav className="space-y-0.5 font-mono text-xs uppercase tracking-widest flex-grow">
					<button onClick={() => { setActiveTab('analytics'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'analytics' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
						System Analytics
					</button>
					<button onClick={() => { setActiveTab('users'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'users' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
						System Users
					</button>
					<button onClick={() => { setActiveTab('researchers'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'researchers' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
						Researchers Mgmt
					</button>
					<button onClick={() => { setActiveTab('articles'); setArticlesTab('post'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'articles' && articlesTab === 'post' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
						Articles Mgmt
					</button>
					<button onClick={() => { setActiveTab('articles'); setArticlesTab('healthedia_ext_res'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'articles' && articlesTab === 'healthedia_ext_res' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
						External Research
					</button>
					<button onClick={() => { setActiveTab('articles'); setArticlesTab('healthedia_journal'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'articles' && articlesTab === 'healthedia_journal' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
						Scientific Journal
					</button>
					<button onClick={() => { setActiveTab('certificates'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'certificates' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
						Certificates
					</button>
					<button onClick={() => { setActiveTab('verifications'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'verifications' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
						Verify Requests
					</button>
					<button onClick={() => { setActiveTab('settings'); closeSidebar(); }} className={`w-full text-left px-3 py-2 rounded-lg transition-colors flex items-center gap-3 ${activeTab === 'settings' ? 'bg-black text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-black'}`}>
						<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
						Global Settings
					</button>
				</nav>

				<a href="/" className="block mt-8 px-4 py-4 border border-[#E0E0E0] text-gray-500 hover:text-black hover:border-black rounded-xl transition-colors text-center font-mono text-xs uppercase tracking-widest">← Back to Site</a>
			</aside>

			<main className="flex-grow overflow-y-auto w-full md:w-[calc(100%-16rem)] flex flex-col">
				<header className="sticky top-0 bg-white/90 backdrop-blur-sm z-30 border-b border-[#E0E0E0] px-6 py-4 flex items-center justify-between hidden md:flex">
					<h2 className="font-sans font-bold uppercase tracking-widest text-sm text-gray-500">Healthedia Administrator Dashboard</h2>
					<div className="flex items-center gap-4">
						<a href="/" target="_blank" rel="noreferrer" className="text-gray-500 hover:text-black font-mono text-[10px] uppercase tracking-widest flex items-center gap-1 transition-colors">
							<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
							Visit Website
						</a>
						<span className="w-px h-4 bg-gray-300"></span>
						<a href="/wp-login.php?action=logout" className="text-red-500 hover:text-red-700 font-mono text-[10px] uppercase tracking-widest flex items-center gap-1 transition-colors">
							<svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
							Logout
						</a>
					</div>
				</header>
				<div className="p-4 md:p-12 flex-grow">
				{activeTab === 'analytics' && (
					<>
						<header className="mb-8 md:mb-12 border-b border-[#E0E0E0] pb-6">
							<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">System Analytics</h2>
						</header>
						<div className="grid grid-cols-1 md:grid-cols-3 gap-6">
							<div className="bg-white p-6 border border-[#E0E0E0] rounded-2xl shadow-sm">
								<div className="font-mono text-[10px] text-gray-500 uppercase tracking-widest mb-3">Total Users</div>
								<div className="text-4xl font-bold font-sans tracking-tighter break-words">{stats.users.toLocaleString()}</div>
							</div>
							<div className="bg-white p-6 border border-[#E0E0E0] rounded-2xl shadow-sm">
								<div className="font-mono text-[10px] text-gray-500 uppercase tracking-widest mb-3">Indexed Articles</div>
								<div className="text-4xl font-bold font-sans tracking-tighter break-words">{stats.articles.toLocaleString()}</div>
							</div>
							<div className="bg-white p-6 border border-[#E0E0E0] rounded-2xl shadow-sm">
								<div className="font-mono text-[10px] text-gray-500 uppercase tracking-widest mb-3">Global Views</div>
								<div className="text-4xl font-bold font-sans tracking-tighter break-words">{stats.total_views.toLocaleString()}</div>
							</div>
						</div>
					</>
				)}

				{activeTab === 'users' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
							<div>
								<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">System Users Management</h2>
								<div className="mt-4 relative">
									<input type="text" placeholder="Search users by name or email..." value={userSearch} onChange={e => setUserSearch(e.target.value)} className="w-full md:w-80 border border-[#E0E0E0] rounded-lg pl-10 pr-4 py-2 text-sm font-sans outline-none focus:border-black" />
									<svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
								</div>
							</div>
							<button onClick={() => { setEditingUser(null); setIsRestrictedChecked(false); setShowUserModal(true); }} className="bg-black text-white px-4 py-2 rounded-full font-mono text-[10px] uppercase tracking-widest hover:bg-gray-800 transition-colors">Add User</button>
						</header>
						<div className="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
							<table className="w-full text-left border-collapse min-w-[600px]">
								<thead>
									<tr className="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
										<th className="py-4 px-6 font-normal">UID</th>
										<th className="py-4 px-6 font-normal">Name</th>
										<th className="py-4 px-6 font-normal">Email</th>
										<th className="py-4 px-6 font-normal">Status</th>
										<th className="py-4 px-6 font-normal">Roles</th>
										<th className="py-4 px-6 font-normal">Registered</th>
										<th className="py-4 px-6 font-normal text-right">Actions</th>
									</tr>
								</thead>
								<tbody className="font-sans text-sm divide-y divide-[#E0E0E0]">
									{users.filter(u => u.name.toLowerCase().includes(userSearch.toLowerCase()) || u.email.toLowerCase().includes(userSearch.toLowerCase())).map(u => (
										<tr key={u.id} className="hover:bg-gray-50 transition-colors">
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{u.id}</td>
											<td className="py-4 px-6 font-bold whitespace-nowrap">{u.name}</td>
											<td className="py-4 px-6 font-mono text-xs">{u.email}</td>
											<td className="py-4 px-6">
												{u.is_restricted ? <span className="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Restricted</span> : <span className="bg-green-100 text-green-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Active</span>}
											</td>
											<td className="py-4 px-6 font-mono text-[10px] uppercase">{u.roles.join(', ')}</td>
											<td className="py-4 px-6 font-mono text-xs">{new Date(u.registered).toLocaleDateString()}</td>
											<td className="py-4 px-6 text-right space-x-2 whitespace-nowrap">
												<button title="Edit" onClick={() => {
													setEditingUser(u);
													setIsRestrictedChecked(u.is_restricted);
													setShowUserModal(true);
												}} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-black hover:border-black transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
												<button title="Delete" onClick={() => deleteUser(u.id)} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-red-500 hover:border-red-500 hover:bg-red-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
											</td>
										</tr>
									))}
								</tbody>
							</table>
						</div>
					</>
				)}

				{activeTab === 'researchers' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
							<div>
								<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">Researchers Management</h2>
								<div className="mt-4 relative">
									<input type="text" placeholder="Search researchers..." value={researcherSearch} onChange={e => setResearcherSearch(e.target.value)} className="w-full md:w-80 border border-[#E0E0E0] rounded-lg pl-10 pr-4 py-2 text-sm font-sans outline-none focus:border-black" />
									<svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
								</div>
							</div>
							<button onClick={() => { setEditingResearcher(null); setShowResearcherModal(true); }} className="bg-black text-white px-4 py-2 rounded-full font-mono text-[10px] uppercase tracking-widest hover:bg-gray-800 transition-colors">Add Researcher</button>
						</header>
						<div className="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
							<table className="w-full text-left border-collapse min-w-[800px]">
								<thead>
									<tr className="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
										<th className="py-4 px-6 font-normal">UID</th>
										<th className="py-4 px-6 font-normal">Name</th>
										<th className="py-4 px-6 font-normal">Verified</th>
										<th className="py-4 px-6 font-normal">Specialty</th>
										<th className="py-4 px-6 font-normal">Institution</th>
										<th className="py-4 px-6 font-normal">Type</th>
										<th className="py-4 px-6 font-normal text-right">Actions</th>
									</tr>
								</thead>
								<tbody className="font-sans text-sm divide-y divide-[#E0E0E0]">
									{researchers.filter(r => r.name.toLowerCase().includes(researcherSearch.toLowerCase()) || r.specialty.toLowerCase().includes(researcherSearch.toLowerCase())).map(r => (
										<tr key={r.id} className="hover:bg-gray-50 transition-colors">
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{r.id}</td>
											<td className="py-4 px-6 font-bold whitespace-nowrap">{r.name}</td>
											<td className="py-4 px-6">
												{r.is_verified ? <span className="text-green-600"><svg className="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"></path></svg></span> : <span className="text-gray-300">-</span>}
											</td>
											<td className="py-4 px-6 font-mono text-xs truncate max-w-[200px]">{r.specialty}</td>
											<td className="py-4 px-6 font-mono text-xs truncate max-w-[200px]">{r.institution}</td>
											<td className="py-4 px-6">
												{r.is_mock ? <span className="bg-gray-200 text-black px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Mock Data</span> : <span className="bg-black text-white px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Real</span>}
											</td>
											<td className="py-4 px-6 text-right space-x-2 whitespace-nowrap">
												<button title="Edit" onClick={() => { setEditingResearcher(r); setShowResearcherModal(true); }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-black hover:border-black transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
												<button title="Delete" onClick={() => deleteResearcher(r.id)} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-red-500 hover:border-red-500 hover:bg-red-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
											</td>
										</tr>
									))}
								</tbody>
							</table>
						</div>
					</>
				)}

				{activeTab === 'articles' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
							<div>
								<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">
									{articlesTab === 'post' ? 'Articles Management' : (articlesTab === 'healthedia_ext_res' ? 'External Research' : 'Scientific Journal')}
								</h2>
								<div className="mt-4 flex flex-wrap gap-4 items-center">
									<div className="relative">
										<input type="text" placeholder="Search articles..." value={articleSearch} onChange={e => setArticleSearch(e.target.value)} className="w-full md:w-64 border border-[#E0E0E0] rounded-lg pl-10 pr-4 py-2 text-sm font-sans outline-none focus:border-black" />
										<svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
									</div>
									<select value={articleStatusFilter} onChange={e => setArticleStatusFilter(e.target.value)} className="border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm font-sans outline-none focus:border-black bg-white">
										<option value="all">All Statuses</option>
										<option value="publish">Published</option>
										<option value="pending">Pending</option>
										<option value="draft">Draft</option>
									</select>
									<select value={articleCategoryFilter} onChange={e => setArticleCategoryFilter(e.target.value)} className="border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm font-sans outline-none focus:border-black bg-white">
										<option value="all">All Categories</option>
										{[...new Set(articles.flatMap(a => a.categories))].map(cat => (
											<option key={cat} value={cat}>{cat}</option>
										))}
									</select>
									<select value={articleSort} onChange={e => setArticleSort(e.target.value)} className="border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm font-sans outline-none focus:border-black bg-white">
										<option value="desc">Newest First</option>
										<option value="asc">Oldest First</option>
									</select>
								</div>
							</div>
							{selectedArticles.length > 0 && (
								<div className="flex gap-2">
									<button onClick={async () => { await apiFetch('articles/bulk', { method: 'POST', body: JSON.stringify({action: 'publish', ids: selectedArticles}) }); setSelectedArticles([]); loadData(); }} className="bg-black text-white px-4 py-2 rounded-lg font-mono text-[10px] uppercase tracking-widest hover:bg-gray-800 transition-colors">Publish</button>
									<button onClick={async () => { await apiFetch('articles/bulk', { method: 'POST', body: JSON.stringify({action: 'draft', ids: selectedArticles}) }); setSelectedArticles([]); loadData(); }} className="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-mono text-[10px] uppercase tracking-widest hover:bg-yellow-200 transition-colors">Draft</button>
									<button onClick={async () => { if(window.confirm('Delete selected?')) { await apiFetch('articles/bulk', { method: 'POST', body: JSON.stringify({action: 'delete', ids: selectedArticles}) }); setSelectedArticles([]); loadData(); } }} className="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-mono text-[10px] uppercase tracking-widest hover:bg-red-200 transition-colors">Delete</button>
								</div>
							)}
						</header>
						<div className="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
							<table className="w-full text-left border-collapse min-w-[800px]">
								<thead>
									<tr className="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
										<th className="py-4 px-6 font-normal w-10">
											<input type="checkbox" onChange={e => setSelectedArticles(e.target.checked ? articles.map(a => a.id) : [])} checked={selectedArticles.length === articles.length && articles.length > 0} className="rounded border-gray-300 text-black focus:ring-black" />
										</th>
										<th className="py-4 px-6 font-normal">ID</th>
										<th className="py-4 px-6 font-normal">Title</th>
										<th className="py-4 px-6 font-normal">Author</th>
										<th className="py-4 px-6 font-normal">Status</th>
										<th className="py-4 px-6 font-normal">Date</th>
										<th className="py-4 px-6 font-normal text-right">Actions</th>
									</tr>
								</thead>
								<tbody className="font-sans text-sm divide-y divide-[#E0E0E0]">
									{articles
										.filter(a => articleStatusFilter === 'all' || a.status === articleStatusFilter)
										.filter(a => articleCategoryFilter === 'all' || a.categories.includes(articleCategoryFilter))
										.filter(a => a.title.toLowerCase().includes(articleSearch.toLowerCase()) || a.author_name.toLowerCase().includes(articleSearch.toLowerCase()))
										.sort((a, b) => articleSort === 'desc' ? new Date(b.date) - new Date(a.date) : new Date(a.date) - new Date(b.date))
										.map(a => (
										<tr key={a.id} className="hover:bg-gray-50 transition-colors">
											<td className="py-4 px-6">
												<input type="checkbox" checked={selectedArticles.includes(a.id)} onChange={e => setSelectedArticles(e.target.checked ? [...selectedArticles, a.id] : selectedArticles.filter(id => id !== a.id))} className="rounded border-gray-300 text-black focus:ring-black" />
											</td>
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{a.id}</td>
											<td className="py-4 px-6 font-bold truncate max-w-[300px]">
												<a href={a.permalink} target="_blank" rel="noreferrer" className="hover:underline">{a.title}</a>
												<div className="font-mono text-[10px] text-gray-400 font-normal uppercase mt-1">{a.categories.join(', ')}</div>
											</td>
											<td className="py-4 px-6 text-xs">{a.author_name}</td>
											<td className="py-4 px-6">
												{a.status === 'publish' ? <span className="bg-green-100 text-green-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Published</span> :
												 a.status === 'pending' ? <span className="bg-yellow-100 text-yellow-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Pending</span> :
												 <span className="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">{a.status}</span>}
											</td>
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{new Date(a.date).toLocaleDateString()}</td>
											<td className="py-4 px-6 text-right space-x-2 whitespace-nowrap">
												{a.status === 'pending' && <button title="Approve" onClick={async () => { await apiFetch(`articles/${a.id}/status`, { method: 'PUT', body: JSON.stringify({status: 'publish'}) }); loadData(); }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-green-600 hover:border-green-600 hover:bg-green-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg></button>}
												{a.status === 'publish' && <button title="Unpublish" onClick={async () => { await apiFetch(`articles/${a.id}/status`, { method: 'PUT', body: JSON.stringify({status: 'draft'}) }); loadData(); }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-yellow-600 hover:border-yellow-600 hover:bg-yellow-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></button>}
												<button title="Delete" onClick={() => deleteArticle(a.id)} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-red-500 hover:border-red-500 hover:bg-red-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
											</td>
										</tr>
									))}
								</tbody>
							</table>
						</div>
					</>
				)}

				{activeTab === 'certificates' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
							<div>
								<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">Certificates & Documents</h2>
								<div className="mt-4 relative">
									<input type="text" placeholder="Search certificates..." value={certificateSearch} onChange={e => setCertificateSearch(e.target.value)} className="w-full md:w-80 border border-[#E0E0E0] rounded-lg pl-10 pr-4 py-2 text-sm font-sans outline-none focus:border-black" />
									<svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
								</div>
							</div>
							<button onClick={() => { setEditingCertificate(null); setShowCertificateModal(true); }} className="bg-black text-white px-4 py-2 rounded-full font-mono text-[10px] uppercase tracking-widest hover:bg-gray-800 transition-colors">Add Certificate</button>
						</header>
						<div className="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
							<table className="w-full text-left border-collapse min-w-[800px]">
								<thead>
									<tr className="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
										<th className="py-4 px-6 font-normal">ID</th>
										<th className="py-4 px-6 font-normal">Title</th>
										<th className="py-4 px-6 font-normal">Cert Number</th>
										<th className="py-4 px-6 font-normal">Holder Name</th>
										<th className="py-4 px-6 font-normal">Issue Date</th>
										<th className="py-4 px-6 font-normal">Status</th>
										<th className="py-4 px-6 font-normal text-right">Actions</th>
									</tr>
								</thead>
								<tbody className="font-sans text-sm divide-y divide-[#E0E0E0]">
									{certificates.filter(c => c.title.toLowerCase().includes(certificateSearch.toLowerCase()) || (c.cert_number && c.cert_number.toLowerCase().includes(certificateSearch.toLowerCase()))).map(c => (
										<tr key={c.id} className="hover:bg-gray-50 transition-colors">
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{c.id}</td>
											<td className="py-4 px-6 font-bold truncate max-w-[200px]">{c.title}</td>
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{c.cert_number}</td>
											<td className="py-4 px-6 font-mono text-xs truncate max-w-[200px]">{c.holder_name}</td>
											<td className="py-4 px-6 font-mono text-xs">{c.issue_date}</td>
											<td className="py-4 px-6">
												{c.status === 'publish' ? <span className="bg-green-100 text-green-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Active</span> : <span className="bg-gray-200 text-gray-600 px-2 py-0.5 rounded text-[10px] font-mono uppercase tracking-widest">Inactive</span>}
											</td>
											<td className="py-4 px-6 text-right space-x-2 whitespace-nowrap">
												<button title="Edit" onClick={() => { setEditingCertificate(c); setShowCertificateModal(true); }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-black hover:border-black transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
												<button title="Delete" onClick={() => deleteCertificate(c.id)} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-red-500 hover:border-red-500 hover:bg-red-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
											</td>
										</tr>
									))}
								</tbody>
							</table>
						</div>
					</>
				)}

				{activeTab === 'verifications' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
							<div>
								<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight">Verification Requests</h2>
							</div>
						</header>
						<div className="bg-white border border-[#E0E0E0] rounded-2xl shadow-sm overflow-x-auto">
							<table className="w-full text-left border-collapse min-w-[800px]">
								<thead>
									<tr className="bg-gray-50 border-b border-[#E0E0E0] font-mono text-[10px] uppercase tracking-widest text-gray-500">
										<th className="py-4 px-6 font-normal">UID</th>
										<th className="py-4 px-6 font-normal">Name</th>
										<th className="py-4 px-6 font-normal">Specialty</th>
										<th className="py-4 px-6 font-normal">Institution</th>
										<th className="py-4 px-6 font-normal">Date Requested</th>
										<th className="py-4 px-6 font-normal">Document</th>
										<th className="py-4 px-6 font-normal text-right">Actions</th>
									</tr>
								</thead>
								<tbody className="font-sans text-sm divide-y divide-[#E0E0E0]">
									{verificationRequests.length === 0 ? (
										<tr><td colSpan="7" className="py-8 text-center text-gray-500 font-mono text-xs">No pending requests.</td></tr>
									) : verificationRequests.map(v => (
										<tr key={v.id} className="hover:bg-gray-50 transition-colors">
											<td className="py-4 px-6 font-mono text-xs text-gray-500">{v.id}</td>
											<td className="py-4 px-6 font-bold">
												{v.name}
												<div className="font-mono text-[10px] font-normal text-gray-500">{v.email}</div>
											</td>
											<td className="py-4 px-6 font-mono text-xs">{v.specialty}</td>
											<td className="py-4 px-6 font-mono text-xs">{v.institution}</td>
											<td className="py-4 px-6 font-mono text-xs">{new Date(v.date * 1000).toLocaleString()}</td>
											<td className="py-4 px-6">
												{v.document_url ? <a href={v.document_url} target="_blank" rel="noreferrer" className="text-blue-600 hover:underline flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg> View</a> : 'None'}
											</td>
											<td className="py-4 px-6 text-right space-x-2 whitespace-nowrap">
												<button title="Approve" onClick={async () => { if(window.confirm('Approve this request?')) { await apiFetch(`verifications/${v.id}/approve`, { method: 'POST' }); loadData(); } }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-green-600 hover:border-green-600 hover:bg-green-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"></path></svg></button>
												<button title="Reject" onClick={async () => { const reason = window.prompt('Reason for rejection:'); if(reason !== null) { await apiFetch(`verifications/${v.id}/reject`, { method: 'POST', body: JSON.stringify({reason}) }); loadData(); } }} className="w-8 h-8 inline-flex items-center justify-center bg-white border border-[#E0E0E0] rounded-lg text-gray-400 hover:text-red-500 hover:border-red-500 hover:bg-red-50 transition-colors"><svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
											</td>
										</tr>
									))}
								</tbody>
							</table>
						</div>
					</>
				)}

				{activeTab === 'settings' && (
					<>
						<header className="mb-6 md:mb-8 border-b border-[#E0E0E0] pb-6">
							<h2 className="text-2xl md:text-3xl font-bold uppercase tracking-tight mb-4">Global System Settings</h2>
							<div className="flex space-x-4 font-mono text-xs uppercase tracking-widest border-b border-[#E0E0E0] pb-0">
								<button
									className={`pb-3 border-b-2 transition-colors ${settingsTab === 'general' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'}`}
									onClick={() => setSettingsTab('general')}
								>General</button>
								<button
									className={`pb-3 border-b-2 transition-colors ${settingsTab === 'authentication' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'}`}
									onClick={() => setSettingsTab('authentication')}
								>Authentication</button>
								<button
									className={`pb-3 border-b-2 transition-colors ${settingsTab === 'data' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black'}`}
									onClick={() => setSettingsTab('data')}
								>Data Management</button>
							</div>
						</header>

						<form onSubmit={saveSettings} className="max-w-2xl bg-white border border-[#E0E0E0] rounded-2xl p-8">
							{settingsTab === 'general' && (
								<div className="space-y-6">
									<div>
										<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Site Name</label>
										<input type="text" value={settings.site_name} onChange={e => setSettings({...settings, site_name: e.target.value})} className="w-full border border-[#E0E0E0] rounded-xl px-4 py-2 font-sans outline-none focus:border-black" />
									</div>
									<div>
										<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Global Description</label>
										<textarea value={settings.site_desc} onChange={e => setSettings({...settings, site_desc: e.target.value})} className="w-full border border-[#E0E0E0] rounded-xl px-4 py-2 font-sans outline-none focus:border-black" rows="3"></textarea>
									</div>
									<div>
										<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Admin Contact Email</label>
										<input type="email" value={settings.admin_email} onChange={e => setSettings({...settings, admin_email: e.target.value})} className="w-full border border-[#E0E0E0] rounded-xl px-4 py-2 font-sans outline-none focus:border-black" />
									</div>
									<div className="pt-4 border-t border-[#E0E0E0]">
										<h3 className="text-lg font-bold uppercase tracking-tight mb-4">Footer Legal Pages</h3>
										<div className="space-y-4">
											<div>
												<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Privacy Policy URL</label>
												<input type="url" value={settings.privacy_policy_url} onChange={e => setSettings({...settings, privacy_policy_url: e.target.value})} placeholder="e.g. https://healthedia.org/privacy" className="w-full border border-[#E0E0E0] rounded-xl px-4 py-2 font-sans outline-none focus:border-black" />
											</div>
											<div>
												<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Terms of Service URL</label>
												<input type="url" value={settings.terms_url} onChange={e => setSettings({...settings, terms_url: e.target.value})} placeholder="e.g. https://healthedia.org/terms" className="w-full border border-[#E0E0E0] rounded-xl px-4 py-2 font-sans outline-none focus:border-black" />
											</div>
										</div>
									</div>
								</div>
							)}

							{settingsTab === 'authentication' && (
								<div className="space-y-6">
									<h3 className="text-lg font-bold uppercase tracking-tight mb-4">Authentication Configuration</h3>
									<div className="space-y-4">
										<label className="flex items-center gap-3">
											<input type="checkbox" checked={settings.enable_registration === 'yes'} onChange={e => setSettings({...settings, enable_registration: e.target.checked ? 'yes' : 'no'})} className="w-4 h-4 text-black focus:ring-black border-gray-300 rounded" />
											<span className="font-mono text-sm text-gray-700">Enable New User Registration</span>
										</label>
										<label className="flex items-center gap-3">
											<input type="checkbox" checked={settings.auth_maintenance_mode === 'yes'} onChange={e => setSettings({...settings, auth_maintenance_mode: e.target.checked ? 'yes' : 'no'})} className="w-4 h-4 text-black focus:ring-black border-gray-300 rounded" />
											<span className="font-mono text-sm text-gray-700">Enable Auth Maintenance Mode (Disables Login & Registration)</span>
										</label>
									</div>
								</div>
							)}

							{settingsTab === 'data' && (
								<div className="space-y-6">
									<h3 className="text-lg font-bold uppercase tracking-tight mb-4">Data Management</h3>
									<div className="space-y-4">
										<p className="font-sans text-sm text-gray-600 mb-4">Manage mock data seeded during installation. This action cannot be undone.</p>
										{settings.mock_data_seeded ? (
											<button type="button" onClick={wipeMockData} className="border border-red-500 text-red-500 px-6 py-2 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-red-50 transition-colors">Wipe Mock Data</button>
										) : (
											<p className="font-mono text-xs text-green-600 uppercase tracking-widest">Mock Data is currently clean.</p>
										)}
									</div>
								</div>
							)}

							<div className="pt-8 mt-8 flex justify-between items-center border-t border-[#E0E0E0]">
								<button type="submit" className="bg-black text-white px-6 py-2 rounded-full font-sans uppercase text-sm tracking-wide hover:bg-gray-800 transition-colors">Save Settings</button>
							</div>
						</form>
					</>
				)}

			{/* Modals */}
			{showUserModal && (
				<div className="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
					<form onSubmit={handleUserSubmit} className="bg-white rounded-2xl p-6 md:p-8 w-full max-w-md">
						<h3 className="text-xl font-bold uppercase tracking-tight mb-6">{editingUser ? 'Edit User' : 'Add User'}</h3>
						<div className="space-y-4 mb-6">
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Name</label>
								<input type="text" name="name" defaultValue={editingUser?.name || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Email</label>
								<input type="email" name="email" defaultValue={editingUser?.email || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-2">Roles</label>
								<div className="space-y-2 border border-[#E0E0E0] rounded-lg p-3">
									{['member', 'researcher', 'reviewer', 'editor', 'administrator'].map(r => (
										<label key={r} className="flex items-center gap-2">
											<input type="checkbox" name="roles[]" value={r} defaultChecked={editingUser?.roles?.includes(r)} className="w-4 h-4 text-black focus:ring-black border-gray-300 rounded" />
											<span className="font-sans text-sm capitalize">{r}</span>
										</label>
									))}
								</div>
							</div>

							{editingUser && (
								<div className="pt-4 border-t border-[#E0E0E0]">
									<label className="flex items-center gap-3 mb-4">
										<input type="checkbox" name="is_restricted" value="1" checked={isRestrictedChecked} onChange={(e) => setIsRestrictedChecked(e.target.checked)} className="w-4 h-4 text-black focus:ring-black border-gray-300 rounded" />
										<span className="font-sans text-sm font-bold text-red-600">Restrict Account Temporarily</span>
									</label>

									{isRestrictedChecked && (
										<div className="space-y-3 bg-red-50 p-4 rounded-lg border border-red-100">
											<div>
												<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Restriction Duration (Days)</label>
												<input type="number" name="restricted_duration" min="1" defaultValue="7" required={isRestrictedChecked} className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
											</div>
											<div>
												<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Restriction Reason</label>
												<input type="text" name="restricted_reason" defaultValue={editingUser.restricted_reason || ''} required={isRestrictedChecked} placeholder="e.g. Violation of terms" className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
											</div>
											<div>
												<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Administrator Notes (Internal)</label>
												<textarea name="restricted_notes" defaultValue={editingUser.restricted_notes || ''} rows="2" className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black"></textarea>
											</div>
										</div>
									)}
								</div>
							)}
						</div>
						<div className="flex justify-end gap-3">
							<button type="button" onClick={() => setShowUserModal(false)} className="px-4 py-2 rounded-full font-mono text-xs uppercase tracking-widest text-gray-500 hover:bg-gray-100">Cancel</button>
							<button type="submit" className="bg-black text-white px-6 py-2 rounded-full font-mono text-xs uppercase tracking-widest hover:bg-gray-800">Save</button>
						</div>
					</form>
				</div>
			)}

			{showResearcherModal && (
				<div className="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
					<form onSubmit={handleResearcherSubmit} className="bg-white rounded-2xl p-6 md:p-8 w-full max-w-md">
						<h3 className="text-xl font-bold uppercase tracking-tight mb-6">{editingResearcher ? 'Edit Researcher' : 'Add Researcher'}</h3>
						<div className="space-y-4 mb-6">
							{!editingResearcher && (
								<div>
									<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Select User</label>
									<select name="user_id" required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black bg-white">
										<option value="">-- Select a User --</option>
										{users.filter(u => !researchers.some(r => r.id === u.id)).map(u => (
											<option key={u.id} value={u.id}>{u.name} ({u.email})</option>
										))}
									</select>
								</div>
							)}
							{editingResearcher && (
								<div className="bg-gray-50 p-3 rounded-lg border border-[#E0E0E0] mb-4">
									<div className="font-mono text-[10px] uppercase tracking-widest text-gray-500">Linked User Account</div>
									<div className="font-bold">{editingResearcher.name}</div>
									<div className="text-sm text-gray-500">{editingResearcher.email}</div>
								</div>
							)}
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Specialty</label>
								<input type="text" name="specialty" defaultValue={editingResearcher?.specialty || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Institution</label>
								<input type="text" name="institution" defaultValue={editingResearcher?.institution || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<label className="flex items-center gap-3 pt-2">
								<input type="checkbox" name="is_verified" value="1" defaultChecked={editingResearcher?.is_verified} className="w-4 h-4 text-black focus:ring-black border-gray-300 rounded" />
								<span className="font-sans text-sm font-bold flex items-center gap-1 text-gray-700">Verified Badge <svg className="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 20 20"><path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd"></path></svg></span>
							</label>
						</div>
						<div className="flex justify-end gap-3">
							<button type="button" onClick={() => setShowResearcherModal(false)} className="px-4 py-2 rounded-full font-mono text-xs uppercase tracking-widest text-gray-500 hover:bg-gray-100">Cancel</button>
							<button type="submit" className="bg-black text-white px-6 py-2 rounded-full font-mono text-xs uppercase tracking-widest hover:bg-gray-800">Save</button>
						</div>
					</form>
				</div>
			)}

			{showCertificateModal && (
				<div className="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
					<form onSubmit={handleCertificateSubmit} className="bg-white rounded-2xl p-6 md:p-8 w-full max-w-md">
						<h3 className="text-xl font-bold uppercase tracking-tight mb-6">{editingCertificate ? 'Edit Certificate' : 'Add Certificate'}</h3>
						<div className="space-y-4 mb-6">
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Document Title</label>
								<input type="text" name="title" defaultValue={editingCertificate?.title || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Certificate / Verification Number</label>
								<input type="text" name="cert_number" defaultValue={editingCertificate?.cert_number || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Holder Name</label>
								<input type="text" name="holder_name" defaultValue={editingCertificate?.holder_name || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Issue Date</label>
								<input type="date" name="issue_date" defaultValue={editingCertificate?.issue_date || ''} required className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black" />
							</div>
							<div>
								<label className="block font-mono text-[10px] uppercase tracking-widest text-gray-500 mb-1">Status</label>
								<select name="status" defaultValue={editingCertificate?.status || 'publish'} className="w-full border border-[#E0E0E0] rounded-lg px-3 py-2 text-sm outline-none focus:border-black bg-white">
									<option value="publish">Active</option>
									<option value="draft">Inactive</option>
								</select>
							</div>
						</div>
						<div className="flex justify-end gap-3">
							<button type="button" onClick={() => setShowCertificateModal(false)} className="px-4 py-2 rounded-full font-mono text-xs uppercase tracking-widest text-gray-500 hover:bg-gray-100">Cancel</button>
							<button type="submit" className="bg-black text-white px-6 py-2 rounded-full font-mono text-xs uppercase tracking-widest hover:bg-gray-800">Save</button>
						</div>
					</form>
				</div>
			)}
				</div>
			</main>
		</div>
	);
};

const rootEl = document.getElementById('healthedia-dashboard-root');
if (rootEl) {
	const root = createRoot(rootEl);
	root.render(<Dashboard />);
}
