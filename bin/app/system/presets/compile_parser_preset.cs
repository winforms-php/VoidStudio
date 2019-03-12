public class VoidControlsParser
{
    public static void parseControls (string group, Control control)
    {
        WinForms_PHP.Program.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + control.Name + "'] = " + WinForms_PHP.Program.HashByObject (control) + ";");

        var cs = control.GetType ().GetFields (BindingFlags.Instance | BindingFlags.Public | BindingFlags.NonPublic).Where (f => typeof (Component).IsAssignableFrom (f.FieldType));

        foreach (var c in cs)
            WinForms_PHP.Program.eval ("$GLOBALS['__underConstruction']['" + group + "']['" + c.Name + "'] = " + WinForms_PHP.Program.HashByObject (c.GetValue (control)) + ";");
    }
}