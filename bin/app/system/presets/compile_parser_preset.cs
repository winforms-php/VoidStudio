public class VoidControlsParser
{
    public static void ParseControls (string Group, Control Control)
    {
        WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + Group + "']['" + Control.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (Control) + ";");

        var Cs = Control.GetType ().GetFields (BindingFlags.Instance | BindingFlags.Public | BindingFlags.NonPublic)
            .Where (F => typeof (Component).IsAssignableFrom (F.FieldType));

        foreach (var C in Cs)
            WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + Group + "']['" + C.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (C.GetValue (Control)) + ";");
    }
	
	public static void ParseControlsForOpening (string Group, Control Control)
    {
        WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + Group + "']['" + Control.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (Control) + ";");

        var Cs = Control.GetType ().GetFields (BindingFlags.Instance | BindingFlags.Public | BindingFlags.NonPublic)
            .Where (F => typeof (Component).IsAssignableFrom (F.FieldType));

        foreach (var C in Cs)
		{
            WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + Group + "']['" + C.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (C.GetValue (Control)) + ";");

			C.SetValue (Control, null);
		}
    }
}