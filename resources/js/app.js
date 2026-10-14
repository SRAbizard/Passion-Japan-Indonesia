import { runIntro } from './intro';
import './home';

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', runIntro);
else runIntro();
