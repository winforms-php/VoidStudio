string[] forms = new string[] {"%forms%"};

foreach (string form in forms)
    VoidControlsParser.parseControls (form, (Control) Activator.CreateInstance (Type.GetType (form)));