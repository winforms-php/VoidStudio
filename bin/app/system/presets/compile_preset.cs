string[] forms = {"%forms%"};

foreach (string form in forms)
    VoidControlsParser.parseControls (form, Activator.CreateInstance (Type.GetType (form)));