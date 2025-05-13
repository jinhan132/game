package
{
	import flash.display.Bitmap;
	import flash.utils.ByteArray;
	import flash.utils.Endian;
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import VXEngine.*;


	public class resource extends Object
	{
		[Embed( source = "board.png" )]
		private var png0:Class;

		[Embed( source = "icon.png" )]
		private var png1:Class;

		[Embed( source = "wbsall.png" )]
		private var png2:Class;

		public var m_list:CObArray = new CObArray();
		public var m_listSound:CObArray = new CObArray();
		public var m_listUMK:CObArray = new CObArray();
		public var m_listVXA:CObArray = new CObArray();

		public function resource()
		{
			var layer:Bitmap;
			var snd:Sound;
			var buffer:ByteArray;
			var buffer2:ByteArray;
			var buffer3:ByteArray;
			layer = new png0();	AddPng( layer, "board.png", 50, 200, 0, 0, 0, 0, 0x00ff00ff, 0x10000020 );
			layer = new png1();	AddPng( layer, "icon.png", 0, 0, 0, 0, 0, 0, 0x00ff00ff, 0x10000020 );
			layer = new png2();	AddPng( layer, "wbsall.png", 0, 0, 0, 0, 0, 0, 0x00ff00ff, 0x10000020 );
		}

		public function AddPng(bmp:Bitmap, name:String, dx:int = 0, dy:int = 0, ix:int = 0, iy:int = 0, iw:int = 0, ih:int = 0, mask:uint = 0x00ff00ff, flags:uint = 0) : void {
			var image:CVXImage = new CVXImage( bmp.bitmapData, name, dx, dy, ix, iy, iw, ih, mask, flags );
			m_list.AddTail( image );
		}
		public function AddJpg(bmp:Bitmap, name:String, dx:int = 0, dy:int = 0, ix:int = 0, iy:int = 0, iw:int = 0, ih:int = 0, mask:uint = 0x00ff00ff, flags:uint = 0) : void {
			var image:CVXImage = new CVXImage( bmp.bitmapData, name, dx, dy, ix, iy, iw, ih, mask, flags );
			m_list.AddTail( image );
		}
		public function AddGif(buf:ByteArray, name:String, dx:int = 0, dy:int = 0, ix:int = 0, iy:int = 0, iw:int = 0, ih:int = 0, mask:uint = 0x00ff00ff, flags:uint = 0) : void {
			var image:CVXImage = CVXImage.GIF( buf, name, dx, dy, ix, iy, iw, ih, mask, flags );
			m_list.AddTail( image );
		}
		public function AddSound(snd:Sound, name:String) : void {
			var sndObj:CVXSound = new CVXSound( snd, name );
			m_listSound.AddTail( sndObj );
		}
		public function AddUMK(buf:ByteArray, name:String) : void {
			buf.endian = Endian.LITTLE_ENDIAN;
			var obj:CAction = new CAction( buf, name );
			m_listUMK.AddTail( obj );
		}
		public function AddVXA(buf:ByteArray, name:String) : void {
			buf.endian = Endian.LITTLE_ENDIAN;
			var obj:CMapInfo = new CMapInfo( buf, name );
			m_listVXA.AddTail( obj );
		}
	}
}
