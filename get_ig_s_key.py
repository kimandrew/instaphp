import frida
import sys

session = frida.get_usb_device(1000000).attach("com.instagram.android")
script = session.create_script("""
fscrambler = Module.findExportByName(null,"_ZN9Scrambler9getStringESs");
Interceptor.attach(ptr(fscrambler), {
   onLeave: function (retval) {
		send("key: " + Memory.readCString(retval));
   }
});
""")

def on_message(message, data):
   print(message)

script.on('message', on_message)
script.load()
sys.stdin.read()
