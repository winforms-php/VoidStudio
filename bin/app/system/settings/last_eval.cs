/*MessageBox.Show ("Are you sure?", "Confirm", MessageBoxButtons.YesNo, MessageBoxIcon.Question);

Form form = new Form ();
form.Show ();*/

var url = "https://raw.githubusercontent.com/KRypt0nn/WinForms-PHP/master/bin/app/license.txt";

var textFromFile = (new System.Net.WebClient ()).DownloadString (url);

MessageBox.Show (textFromFile);