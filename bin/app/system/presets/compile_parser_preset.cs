public class VoidControlsParser
{
    public static void parseControls (string group, Control control)
    {
        WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + control.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (control) + ";");

        var cs = control.GetType ().GetFields (BindingFlags.Instance | BindingFlags.Public | BindingFlags.NonPublic).Where (f => typeof (Component).IsAssignableFrom (f.FieldType));

        foreach (var c in cs)
            WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + c.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (c.GetValue (control)) + ";");
    }
	
	public static void parseControlsForOpening (string group, Control control)
    {
        WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + control.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (control) + ";");

        var cs = control.GetType ().GetFields (BindingFlags.Instance | BindingFlags.Public | BindingFlags.NonPublic).Where (f => typeof (Component).IsAssignableFrom (f.FieldType));

        foreach (var c in cs)
		{
            WinForms_PHP.ZendProgram.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + c.Name + "'] = " + WinForms_PHP.ZendProgram.HashByObject (c.GetValue (control)) + ";");

			c.SetValue (control, null);
		}
    }
}