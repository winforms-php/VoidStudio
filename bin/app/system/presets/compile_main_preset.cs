string[] Forms = new string[] {"%forms%"};

foreach (string Form in Forms)
    VoidControlsParser.ParseControls (Form, (Control) Activator.CreateInstance (Type.GetType (Form)));